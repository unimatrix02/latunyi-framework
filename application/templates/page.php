<!DOCTYPE html>
<html>
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
		<div class="row">
			<h1>
				Framework Test
			</h1>
			<nav>
				<ul>
					<? foreach ($menu as $path => $label): ?>
					<li>
						<a <? if ($path == $reqPath): ?>class="current"<?endif; ?> href="<?=$path?>"><?= $label ?></a>
					</li>
					<? endforeach ?>
					<li><a href="#">Test menu</a>
						<ul>
							<li><a href="#">Item 1</a></li>
							<li><a href="#">Item 2</a>
								<ul>
									<li><a href="#">Item 1</a></li>
									<li><a href="#">Item 2</a></li>
									<li><a href="#">Item 3</a></li>
								</ul>
							</li>
							<li><a href="#">Item 3</a></li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</header>

	<div class="row main">
		<?= $this->render($content); ?>
	</div>

</body>
</html>

