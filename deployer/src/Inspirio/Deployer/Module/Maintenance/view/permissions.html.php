<form action="<?php echo $currentUrl ?>" method="post" class="form-horizontal" data-result-block="#result">
    <div class="control-group">
        <div class="controls">
            <button type="submit" name="run" value="makeAllFilesWritable" class="btn btn-warning">
                <i class="icon-warning-sign"></i> Make all files writable
            </button>

            <button type="submit" name="run" value="cleanFilePermissions" class="btn btn-success">
                <i class="icon-ok-sign"></i> Clean file permissions
            </button>
        </div>
    </div>
</form>
