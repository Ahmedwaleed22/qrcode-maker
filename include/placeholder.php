<nav class="navbar sticky-top">
    <div class="placeresult bg-light d-grid">
        <div class="form-group text-center wrapresult">
            <div class="resultholder">
                <img class="img-fluid" src="<?php echo $relative.qrcdr()->getConfig('placeholder'); ?>" />
                <div class="infopanel"></div>
            </div>
        </div>
        <div class="preloader"><i class="fa fa-cog fa-spin"></i></div>
        <input type="hidden" class="holdresult">
        <div class="form-group file-name">
            <label style="margin-bottom: .2em;" for="file_name">File Name</label>
            <input form="qrcdr-form" style="width: 100%;outline: none;border: 1px solid #d4d4d4;padding: .5em" id="file_name" name="file_name" type="text" placeholder="File Name" value="Untitled">
        </div>
        <button class="btn btn-lg btn-block btn-primary ellipsis generate_qrcode<?php echo $rounded_btn_save; ?>" disabled><i class="fa fa-check"></i> <?php echo qrcdr()->getString('save'); ?></button>
        <div class="text-center mt-2 linksholder"></div>
    </div>
<?php
// if (file_exists(dirname(dirname(__FILE__)).'/'.$relative.'template/sidebar.php')) {
//     include dirname(dirname(__FILE__)).'/'.$relative.'template/sidebar.php';
// }
?>
</nav>