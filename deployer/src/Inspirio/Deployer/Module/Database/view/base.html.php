<ul class="nav nav-tabs">
	<li class="active"><a href="#create" data-toggle="tab">Create</a></li>
	<li><a href="#upload" data-toggle="tab">Upload</a></li>
	<li><a href="#download" data-toggle="tab">Download</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="create">
		<form action="<?php echo $currentUrl ?>" method="post" class="form-horizontal" data-result-block="#database-result">
			<div class="control-group">
				<label class="control-label" for="parametersChange-env">Environment</label>
				<div class="controls">
					<input type="text" name="databaseName" value="<?php echo $data['databaseName'] ?>" />
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button type="submit"
					        name="action"
					        value="create"
					        class="btn btn-primary">
						<i class="icon-plus-sign"></i> Create database
					</button>
				</div>
			</div>
		</form>
	</div>

	<div class="tab-pane" id="upload">
		<form action="<?php echo $currentUrl ?>" method="post" class="form-horizontal" data-result-block="#checkout-result">
			<div class="control-group">
				<label class="control-label" for="parametersChange-databaseName">Database name</label>
				<div class="controls">
					<input type="text" id="parametersChange-databaseName" name="database_name" value="<?php echo $data['database_name']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="parametersChange-databaseHost">Database host</label>
				<div class="controls">
					<input type="text" id="parametersChange-databaseHost" name="database_host" value="<?php echo $data['database_host']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="parametersChange-databaseUser">Database user</label>
				<div class="controls">
					<input type="text" id="parametersChange-databaseUser" name="database_user" value="<?php echo $data['database_user']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="parametersChange-databasePassword">Database password</label>
				<div class="controls">
					<input type="text" id="parametersChange-databasePassword" name="database_password" value="<?php echo $data['database_password']; ?>" />
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button type="submit"
					        name="change"
					        value="custom"
					        class="btn btn-primary">
						<i class="icon-pencil"></i> Change
					</button>
				</div>
			</div>
		</form>
	</div>
</div>

<pre id="database-result"></pre>
