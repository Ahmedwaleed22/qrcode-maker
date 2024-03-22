<?php

include './classes/Dbh.class.php';
include './classes/Link.class.php';

$linkID = filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$link = new Link();
$accesses = $link->track($linkID);

$data = [];

foreach ($accesses as $access) {
  $data[$access['country_code']] = $access['count'];
}

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
  <link rel="stylesheet" href="<?php echo $relative; ?>js/map/jquery-jvectormap.css">
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
  ?>
  <div class="container mt-3">
    <div id="vmap" style="margin: 0 auto; width: 100%; height: 500px"></div>
    <table class="table mt-3">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>IP Address</th>
          <th>Country</th>
          <th>Country Code</th>
          <th>Request Date</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $links = new Link();
        $rows = $links->requests($linkID);

        foreach ($rows as $row) {
        ?>
          <tr>
            <td><?php echo $row['ID']; ?></td>
            <td><?php echo $row['ip_address']; ?></td>
            <td><?php echo $row['country']; ?></td>
            <td><?php echo $row['country_code']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
  </div>
  <script src="<?php echo $relative; ?>js/map/jquery-1.8.2.js"></script>
  <script src="<?php echo $relative; ?>js/map/jquery.jvectormap.min.js"></script>
  <script src="<?php echo $relative; ?>js/map/jquery-jvectormap-world-mill-en.js"></script>
  <script type="text/javascript">
    var countries = '';
    var gdpData = JSON.parse(`<?php echo json_encode($data); ?>`);

    function getCountryData() {
      $.ajax({
        url: 'data/countries.json',
        type: 'get',
        success: function(res) {
          countries = res;
        }
      });
    }
    getCountryData();
    $(document).ready(function() {
      $("#vmap").vectorMap({
        map: 'world_mill_en',
        backgroundColor: '#222',
        borderColor: '#555',
        color: '#555',
        hoverOpacity: 0.7,
        selectedColor: '#666666',
        enableZoom: true,
        enableDrag: true,
        showTooltip: true,
        normalizeFunction: 'polynomial',
        scaleColors: ['#d0c4dc', '#d1b0eb', '#b296cb', '#47006b'],
        series: {
          regions: [{
            values: gdpData,
            scale: ['#C8EEFF', '#0071A4'],
            normalizeFunction: 'polynomial'
          }]
        },
        onRegionTipShow: function(e, el, code) {
          if (gdpData[code]) {
            el.html(el.html() + ' (Requests: ' + gdpData[code] + ')');
          }
        }
      });
    });
  </script>
</body>

</html>