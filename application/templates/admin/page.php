<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Admin - Latunyi Framework</title>

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
		<div class="row">
			<h1>
				Framework Test - Admin
			</h1>
			<nav>
				<ul>
					<li>
						<a href="/">Back to site</a>
					</li>
				</ul>
			</nav>
		</div>
	</header>

	<div class="row main">
		<?= $this->render('admin/' . $content); ?>
	</div>

</body>
</html>

