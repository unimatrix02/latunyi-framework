<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Compare</title>

	<meta name="author" content="Latunyi" />

	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!--[if lt IE 9]>
	<script src="/html/html5shiv.js"></script>
	<![endif]-->

	<? foreach ($stylesheets as $file): ?>
	<link href="/css/<?=$file?>" rel="stylesheet" type="text/css" media="screen">
	<? endforeach; ?>

	<? foreach ($scripts as $file): ?>
	<script type="text/javascript" src="/js/<?=$file?>?t=<?=time()?>"></script>
	<? endforeach; ?>

</head>
<body>

	<div id="wrapper">	

		<div id="header">
			<h1>Framework Test Page</h1>
		</div>
	
		<div id="main">

			<? $this->load($content); ?>
			
		</div>
		
	</div>
	
</body>
</html>

