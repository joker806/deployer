<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project deployer - <?= $app->getProjectInfo()->getName() ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href="public/css/bootstrap.min.css" rel="stylesheet">
	<link href="public/css/font-awesome.min.css" rel="stylesheet">
	<link href="public/css/app.less" rel="stylesheet/less">
	<link rel="shortcut icon" href="public/favicon.ico">
</head>

<body class="<?= isset($bodyClass) ? $bodyClass : '' ?>">
	<?= $subContent ?>

    <script src="public/js/less.min.js"></script>
	<script src="public/js/jquery.min.js"></script>
	<script src="public/js/bootstrap.min.js"></script>
	<script src="public/js/app.js"></script>
</body>
</html>
