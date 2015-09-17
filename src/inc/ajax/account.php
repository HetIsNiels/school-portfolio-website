<?php
require_once __DIR__ . '/../bootstrap.php';

$post = array(
	'passwordCurrent' => (isset($_POST['passwordCurrent']) ? $_POST['passwordCurrent'] : ''),
	'type' => (isset($_POST['type']) ? $_POST['type'] : ''),
	'username' => (isset($_POST['username']) ? $_POST['username'] : ''),
	'password' => (isset($_POST['password']) ? $_POST['password'] : '')
);

if($post['type'] == 'modify' && isset($_COOKIE['authCode7'])) {
	$result = $portfolio->getUserManager()->modify($_COOKIE['authCode7'], $post['username'], $post['password'], $post['passwordCurrent']);
}else{
	$result = [];
}

header('content-type: application/json');
echo json_encode($result);