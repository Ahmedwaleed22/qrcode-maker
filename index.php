<?php

/**
 * QRcdr - php QR Code generator
 * index.php
 *
 * PHP version 5.4+
 *
 * @category  PHP
 * @package   QRcdr
 * @author    Nicola Franchini <info@veno.it>
 * @copyright 2015-2021 Nicola Franchini
 * @license   item sold on codecanyon https://codecanyon.net/item/qrcdr-responsive-qr-code-generator/9226839
 * @version   5.3.5
 * @link      http://veno.es/qrcdr/
 */

include './classes/Dbh.class.php';
include './classes/Link.class.php';

$version = '5.3.5';

if (version_compare(phpversion(), '5.4', '<')) {
    exit("QRcdr requires at least PHP version 5.4.");
}

// https://stackoverflow.com/questions/11920026/replace-file-get-contents-with-curl
if (!ini_get('allow_url_fopen')) {
    exit("Please enable <code>allow_url_fopen<code>");
}
if (!function_exists('mime_content_type')) {
    exit("Please enable the <code>fileinfo</code> extension");
}
// Update this path if you have a custom relative path inside config.php
require dirname(__FILE__) . "/lib/functions.php";

if (qrcdr()->getConfig('debug_mode')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL ^ E_NOTICE);
}
$relative = qrcdr()->relativePath();
require dirname(__FILE__) . '/' . $relative . 'include/head.php';
?>
<!doctype html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $rtl['dir']; ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title><?php echo qrcdr()->getString('title'); ?></title>
    <meta name="description" content="<?php echo qrcdr()->getString('description'); ?>">
    <meta name="keywords" content="<?php echo qrcdr()->getString('tags'); ?>">
    <link rel="shortcut icon" href="<?php echo $relative; ?>images/favicon.ico">
    <link href="<?php echo $relative; ?>bootstrap/css/bootstrap<?php echo $rtl['css']; ?>.min.css" rel="stylesheet">
    <link href="<?php echo $relative; ?>css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $relative; ?>css/style.css">
    <script src="<?php echo $relative; ?>js/jquery-3.5.1.min.js"></script>
    <?php
    $custom_page = false;
    $body_class = '';
    if (isset($_GET['p'])) {
        $load_page = dirname(__FILE__) . '/' . $relative . 'template/' . $_GET['p'] . '.html';
        if (file_exists($load_page)) {
            $custom_page = file_get_contents($load_page);
        }
    }
    qrcdr()->loadQRcdrCSS($version);
    if (!$custom_page) {
        $body_class = 'qrcdr';
        qrcdr()->loadPluginsCss();
    }
    qrcdr()->setMainColor(qrcdr()->getConfig('color_primary'));
    ?>
</head>

<body class="<?php echo $body_class; ?>">
    <?php
    if (file_exists(dirname(__FILE__) . '/' . $relative . 'template/navbar.php')) {
        include dirname(__FILE__) . '/' . $relative . 'template/navbar.php';
    }
    // if (file_exists(dirname(__FILE__).'/'.$relative.'template/header.php')) {
    //     include dirname(__FILE__).'/'.$relative.'template/header.php';
    // }
    if ($custom_page) {
        echo '<div class="container mt-4">' . $custom_page . '</div>';
    } else {
        // Generator
        include dirname(__FILE__) . '/' . $relative . 'include/generator.php';
    }
    qrcdr()->loadQRcdrJS($version);

    if (!$custom_page) {
        qrcdr()->loadPlugins();
    }
    // if (file_exists(dirname(__FILE__).'/'.$relative.'template/footer.php')) {
    //     include dirname(__FILE__).'/'.$relative.'template/footer.php';
    // }
    ?>
    <div style="display: none;" id="popup-container" class="popup-container">
        <div class="popup">
            <span onclick="toggleEditingPopup(0)" class="close-icon">
                <i class="fa fa-close"></i>
            </span>
            <h3>Edit QR Code</h3>
            <form onsubmit="saveChanges(event)">
                <input id="qrcode-link-input" class="form-control" type="hidden" placeholder="Link">
                <div class="form-group">
                    <label for="Name">Name</label>
                    <input id="qrcode-name-input" class="form-control" type="text" placeholder="Name">
                </div>
                <div class="form-group">
                    <label for="Name">Destination</label>
                    <input id="qrcode-destination-input" class="form-control" type="text" placeholder="Destination">
                </div>
                <button class="btn btn-success">Save Changes</button>
            </form>
        </div>
    </div>
    <div class="container mt-3">
        <div class="d-flex flex-row-reverse mb-2">
            <button id="bulk-delete" style="width: 100px" class="btn btn-danger">Delete</button>
        </div>
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Shorten Link</th>
                    <th>Destination</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $links = new Link();
                $rows = $links->all();

                foreach ($rows as $row) {
                ?>
                    <tr>
                        <td><input type="checkbox" data-id="<?php echo $row['ID']; ?>"></td>
                        <td><?php echo $row['ID']; ?></td>
                        <td><?php echo !$row['image'] ? '<span style="color: red">No Image</span>' : '<img style="height: 100px" src="' . $row['image'] . '" alt="QR Code" />' ?></td>
                        <td><?php echo $row['file_name']; ?></td>
                        <td><?php echo $row['link']; ?></td>
                        <td><?php echo $row['destination']; ?></td>
                        <td><?php echo $row['created_date']; ?></td>
                        <td>
                            <button onclick="toggleEditingPopup('<?php echo $row['link']; ?>')" class="btn btn-warning">Edit</button>
                            <button onclick="window.open(`/delete.php?id=<?php echo $row['ID'] ?>`, '_self')" class="btn btn-danger">Delete</button>
                            <button onclick="window.open(`/tracking.php?id=<?php echo $row['ID'] ?>`, '_self')" class="btn btn-info">Track</button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        const checkBoxes = document.querySelectorAll("input[type=checkbox]");
        const bulkDeleteButton = document.getElementById('bulk-delete');
        let IDs = [];

        function toggleEditingPopup(id) {
            const qrcodeName = document.getElementById('qrcode-name-input');
            const qrcodeLink = document.getElementById('qrcode-link-input');
            const qrcodeDestination = document.getElementById('qrcode-destination-input');
            const popupContainer = document.getElementById('popup-container');

            if (popupContainer.style.display === 'none') {
                popupContainer.style.display = 'flex';

                $.ajax({
                    type: "GET",
                    url: `ajax/edit-qrcode.php?page=data&linkID=${id}`,
                    success: function(res) {
                        let qrcodeData = JSON.parse(res);
                        qrcodeName.value = qrcodeData['file_name'];
                        qrcodeLink.value = qrcodeData['link'];
                        qrcodeDestination.value = qrcodeData['destination'];
                    }
                });
            } else {
                popupContainer.style.display = 'none';
            }
        }

        function saveChanges(event) {
            event.preventDefault();
            const qrcodeName = document.getElementById('qrcode-name-input').value;
            const qrcodeLink = document.getElementById('qrcode-link-input').value;
            const qrcodeDestination = document.getElementById('qrcode-destination-input').value;
            const popupContainer = document.getElementById('popup-container');

            $.ajax({
                type: "GET",
                url: `ajax/edit-qrcode.php?page=update&name=${qrcodeName}&linkID=${qrcodeLink}&destination=${qrcodeDestination}`,
                success: function(res) {
                    popupContainer.style.display = 'none';
                    alert('QrCode Edited Successfully!');
                    window.location.reload();
                }
            });
        }

        checkBoxes.forEach((checkbox) => {
            checkbox.addEventListener('change', (e) => {
                const elementID = checkbox.getAttribute('data-id');

                if (e.target.checked) {
                    IDs.push(elementID);
                    console.log(IDs)
                } else {
                    IDs = IDs.filter(id => id !== elementID);
                    console.log(IDs)
                }
            });
        });

        bulkDeleteButton.addEventListener('click', () => {
            if (IDs.length <= 0) {
                alert('Nothing selected to be deleted');
                return;
            }

            const question = confirm('Are you sure want to delete the selected elements? (This Action can\'t be undone)');

            if (question) {
                let dataPreparedForDeletion = IDs.join(',');
                window.open(`/delete.php?id=${dataPreparedForDeletion}`, '_self');
            }
        });
    </script>
</body>

</html>