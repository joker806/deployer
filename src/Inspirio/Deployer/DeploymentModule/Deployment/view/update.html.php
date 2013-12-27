<form action="<?php echo $currentUrl ?>" method="post" class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="update-revision">Revision</label>
		<div class="controls">
			<input type="text" id="update-revision" name="revision" value="HEAD" />
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" name="run" value="update" class="btn btn-primary">
                <i class="icon-refresh"></i> Update
            </button>
		</div>
	</div>
</form>
