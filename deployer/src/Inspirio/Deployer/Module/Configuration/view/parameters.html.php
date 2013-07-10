<form action="<?php echo $currentUrl ?>" method="post" class="form-horizontal">

<div class="row-fluid">
    <fieldset class="span6">
        <legend>Predefined parameters</legend>

        <div class="control-group">
            <label class="control-label" for="parametersChange-env">Environment</label>
            <div class="controls">
                <select id="parametersChange-env" name="env">
                    <option value="prod">Production</option>
                    <option value="test">Test</option>
                    <option value="dev">Development</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <button type="submit" name="run" value="setPredefinedParameters" class="btn btn-primary">
                    <i class="icon-pencil"></i> Change
                </button>
            </div>
        </div>
    </fieldset>

    <fieldset class="span6">
        <legend>Custom parameters</legend>

        <div class="control-group">
            <label class="control-label" for="parametersChange-databaseName">Database name</label>
            <div class="controls">
                <input type="text" id="parametersChange-databaseName" name="databaseName" value="<?php echo $data['databaseName']; ?>" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="parametersChange-databaseHost">Database host</label>
            <div class="controls">
                <input type="text" id="parametersChange-databaseHost" name="databaseHost" value="<?php echo $data['databaseHost']; ?>" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="parametersChange-databaseUser">Database user</label>
            <div class="controls">
                <input type="text" id="parametersChange-databaseUser" name="databaseUser" value="<?php echo $data['databaseUser']; ?>" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="parametersChange-databasePassword">Database password</label>
            <div class="controls">
                <input type="text" id="parametersChange-databasePassword" name="databasePassword" value="<?php echo $data['databasePassword']; ?>" />
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <button type="submit" name="run" value="setCustomParameters" class="btn btn-primary">
                    <i class="icon-pencil"></i> Change
                </button>
            </div>
        </div>
    </fieldset>
</div>



</form>
