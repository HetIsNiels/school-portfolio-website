<?php
require_once __DIR__ . '/../../inc/bootstrap.php';

header('content-type: text/javascript');

$useAuth7 = isset($_GET['authCode7']);

if($useAuth7){
	if($portfolio->getUserManager()->verifyAuthCode7($_GET['authCode7']) === false)
		die;
}else {
	echo 'var pf = {};';
	echo 'pf.webUrl = \'' . $portfolio->getSetting('web.url', '') . '\';';
}

foreach(glob(__DIR__ . '/modules/*.js') as $js){
	echo PHP_EOL . PHP_EOL . '// FILE: ' . str_replace(__DIR__ . '/modules/', '', $js) . PHP_EOL . PHP_EOL;

	$content = file_get_contents($js);
	if(substr($content, 0, 11) != '//USE TOKEN' && !$useAuth7){
		echo $content;
	}else if($useAuth7 && substr($content, 0, 11) == '//USE TOKEN') {
		echo $content;
	}else{
		echo '// PROTECTED';
	}
}

if(!$useAuth7)
	echo 'pf.initialize();';