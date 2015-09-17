<?php
require_once __DIR__ . '/inc/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
	<title>Portfolio van Niels van Velzen</title>

	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?php echo $portfolio->getSetting('web.url', ''); ?>assets/js/iefix.js"></script>
	<![endif]-->

	<meta charset="utf-8" />
	<link rel="stylesheet" href="<?php echo $portfolio->getSetting('web.url', ''); ?>assets/css/style.css" />
	<link rel="stylesheet" href="<?php echo $portfolio->getSetting('web.url', ''); ?>assets/css/font-awesome.min.css" />

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

	<meta name="robots" content="noindex, nofollow" />
	<meta name="author" content="Niels van Velzen" />
	<meta name="keywords" content="Niels, van Velzen, school, ala" />
</head>
<body>
	<header>
		<div id="logo">
			<h1>Niels van Velzen</h1>
			<h2>Applicatieontwikkelaar</h2>
		</div>
	</header>
	<div class="wrapper content">
		<div class="left size-small">
			<div class="box">
				<div class="heading">
					Navigatie
				</div>

				<nav class="inner">
					<div class="item"><a data-page="index">Start</a></div>
					<div class="item"><a data-page="personal">Persoonlijk</a></div>
					<div class="item"><a data-page="study">Opleiding</a></div>
					<div class="item"><a data-page="links">Links</a></div>
				</nav>
			</div>
		</div>
		<div class="right size-large">
			<div id="put-content" data-id="-1"></div>
		</div>
		<div class="clear"></div>
	</div>

	<footer class="inner">
		<div class="left">
			&copy; Niels van Velzen 2014
		</div>
		<div class="right" id="menu-footer-links">
			<a data-page="index">Start</a> |
			<a data-page="personal">Persoonlijk</a> |
			<a data-page="study">Opleiding</a> |
			<a data-page="links">Links</a>
		</div>
		<div class="clear"></div>
	</footer>

	<div id="login-part">
		<div id="cookie"></div>

		<div id="login-dialog" class="popup fixed">
			<div class="heading">
				<div class="left">Inloggen</div>
				<div class="clear"></div>
			</div>
			<div class="notif error" id="login-notif"></div>
			<form method="post" id="login-form">
				<label class="heading" for="login-username">Gebruikersnaam</label>
				<input class="inner" id="login-username" type="text" required="required" />

				<label class="heading" for="login-password">Wachtwoord</label>
				<input class="inner" id="login-password" type="password" required="required" />

				<button type="submit">Inloggen</button>
				<button type="reset">Annuleren</button>
			</form>
		</div>
	</div>

	<script type="text/javascript" src="<?php echo $portfolio->getSetting('web.url', ''); ?>assets/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $portfolio->getSetting('web.url', ''); ?>assets/js/compiler.php"></script>
</body>
</html>