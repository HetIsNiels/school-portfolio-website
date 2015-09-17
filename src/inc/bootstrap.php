<?php
if(version_compare(PHP_VERSION, '5.4.0', '<')){
	echo '<h1>PHP 5.4 or higher required!</h1><i>5.6 is recommended.</i>';
	die;
}

use portfolio\Portfolio;

$settings = [
	'database' => [
		'dsn' => 'sqlite:' . __DIR__ . '/portfolio.db',
		'user' => null,
		'password' => null
	],

	'web' => [
		'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/'
	],

	'auth' => [
		'sessionLength' => '1 day'
	]
];

error_reporting(E_STRICT | E_ALL);
date_default_timezone_set('Europe/Amsterdam');
setlocale(LC_ALL, 'nl_NL', 'nld_nld', 'dutch');
session_start();

// Loading classes
function __autoload($className){
	$classScope = explode('\\', $className);
	$classPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $classScope) . '.php';

	if(is_file($classPath)){
		require_once $classPath;
	}
}

$portfolio = new Portfolio($settings);