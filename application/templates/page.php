<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Latunyi Framework</title>

	<meta name="author" content="Latunyi" />

	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!--[if lt IE 9]>
	<script src="/html/html5shiv.js"></script>
	<![endif]-->

	<? foreach ($styles as $file): ?>
	<link href="<?=$file?>" rel="stylesheet" type="text/css" media="screen">
	<? endforeach; ?>

	<? foreach ($scripts as $file): ?>
	<script type="text/javascript" src="<?=$file?>"></script>
	<? endforeach; ?>

</head>
<body>

	<header>
		<div class="wrapper">
			<div class="nav">
				<a href="/">Normal list</a> |
				<a href="/list">AJAX-loaded list</a>
				<a href="/docs/" style="margin-left: 2em;">PHPDoc</a>
			</div>
			<h1>Framework Test</h1>
		</div>
	</header>
	
	<div class="wrapper">
		<div class="main">
			<?= $this->render($content); ?>
		</div>
	</div>	
	
</body>
</html>

