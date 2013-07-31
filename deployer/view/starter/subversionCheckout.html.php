<?php $this->decorator('starter/decorator.html.php'); ?>

<form method="post" class="form-horizontal">
    <p>Enter project subversion repository</p>

    <div class="control-group">
        <label class="control-label" for="checkout-repo">Repository</label>
        <div class="controls">
            <input class="input-xlarge" type="text" id="checkout-repo" name="repoUrl" value="<?php echo $repoUrl; ?>" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="checkout-repo">Environment</label>
        <div class="controls">
            <select id="checkout-env" name="env">
                <option value="dev">Development</option>
                <option value="test">Testing</option>
                <option value="prod">Production</option>
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="checkout-revision">Revision</label>
        <div class="controls">
            <input type="text" id="checkout-revision" name="revision" value="<?php echo $revision; ?>" />
        </div>
    </div>

    <div class="control-group">
        <div class="controls">
            <button type="submit" name="run" value="checkout" class="btn btn-primary">
                <i class="icon-download"></i> Checkout
            </button>
        </div>
    </div>
</form>
