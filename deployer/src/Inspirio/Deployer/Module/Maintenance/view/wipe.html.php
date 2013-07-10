<form action="<?php echo $currentUrl ?>" method="post" data-result-block="#result">
    <div class="control-group">
        <div class="controls">
            <div class="alert">
                <strong>Warning!</strong> Are you sure you want to do this? All project files will be removed!!!
            </div>

            <button type="submit" name="run" value="wipeProject" class="btn btn-small btn-danger">
                <i class="icon-off"></i> Wipe project
            </button>
            <a class="btn btn-large btn-success" href="/"><i class="icon-ban-circle"></i> No, take me away</a>
        </div>
    </div>
</form>

