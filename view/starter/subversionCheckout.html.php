<form method="post" class="form-horizontal">
    <h3>Subversion checkout</h3>

    <div class="control-group">
        <label class="control-label" for="checkout-repoName">Repository</label>
        <div class="controls">
            <select id="checkout-repoName" name="repoName" required="required">
                <?php
                    foreach($repos as $repo) {
                        $selected = ($repo == $repoName) ? 'selected="selected"' : '';
                        echo "<option value=\"{$repo}\" {$selected}>{$repo}</option>";
                    }
                ?>
            </select>
        </div>
    </div>


    <div class="control-group">
        <label class="control-label" for="checkout-repo">Path</label>
        <div class="controls">
            <input id="checkout-repo" class="input-xlarge" type="text" name="repoPath" required="required" value="<?php echo $repoPath; ?>" />
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="checkout-revision">Revision</label>
        <div class="controls">
            <input id="checkout-revision" class="input-small" type="text" name="revision" required="required" value="<?php echo $revision; ?>" />
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
