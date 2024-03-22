<?php
/**
 * PDF
 */
if (qrcdr()->getConfig('pdf') == true) { ?>
<div class="tab-pane fade <?php if ($getsection === "#pdf") echo "show active"; ?>" id="pdf">
    <h4>pdf</h4>
    <div class="row form-group">
    <div class="col-sm-12">
        <label for="pdf">PDF</label>
        <input type="file" name="pdf" id="pdf" class="form-control" required="required" />
    </div>
    </div>
</div>
    <?php
}
