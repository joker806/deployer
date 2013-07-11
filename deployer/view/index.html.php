<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project deployer</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href="public/css/bootstrap.min.css" rel="stylesheet">
	<link href="public/css/font-awesome.min.css" rel="stylesheet">
	<link href="public/css/app.css" rel="stylesheet">
	<link rel="shortcut icon" href="public/favicon.ico">
</head>

<body>
	<div class="container">
		<div class="masthead">
            <p class="pull-right muted"><?php echo $app->getRootPath() ?></p>
			<h3><a href="?"><?php echo $project['name'] ?> <small><?php echo $project['version'] ?></small></a></h3>
		</div>

		<hr>

		<div class="row-fluid">
			<div class="span3">
				<div class="well sidebar-nav">
					<ul class="nav nav-list">
						<?php
						foreach ($modules as $name => $module) {
                            $class = ($module == $activeModule) ? ' class="active"' : '';

                            echo "<li{$class}>";

                            if ($module->isEnabled()) {
                                echo "<a href=\"?module={$name}\">{$module->getTitle()}</a>";
                            } else {
                                echo $module->getTitle();
                            }

                            echo '</li>';
						}
						?>
					</ul>
				</div>
			</div>

			<div class="span9">
				<?php if ($activeModule) { echo $activeModule->render($request); } ?>
			</div>
		</div>
	</div>

	<script src="public/js/jquery.min.js"></script>
	<script src="public/js/bootstrap.min.js"></script>
	<script src="public/js/app.js"></script>
</body>
</html>
