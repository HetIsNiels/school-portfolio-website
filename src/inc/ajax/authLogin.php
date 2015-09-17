<?php
require_once __DIR__ . '/../bootstrap.php';

$result = array();

$post = array(
	'authCode4' => (isset($_POST['authCode4']) ? $_POST['authCode4'] : ''),
	'authCode7' => (isset($_POST['authCode7']) ? $_POST['authCode7'] : ''),
	'type' => (isset($_POST['type']) ? $_POST['type'] : 'login'),
	'username' => (isset($_POST['username']) ? $_POST['username'] : ''),
	'password' => (isset($_POST['password']) ? $_POST['password'] : '')
);

if($post['type'] == 'login') {
	$result = $portfolio->getUserManager()->tryLogin($post['username'], $post['password']);
}elseif($post['type'] == 'lookup'){
	$result = $portfolio->getUserManager()->lookupActiveSession();
}elseif($post['type'] == 'logout'){
	$portfolio->getUserManager()->logout($post['authCode7']);
	$result = ['code' => 1];
}else{
	$result = [];
}

$result['authCode4'] = $post['authCode4'];

header('content-type: application/json');
echo json_encode($result);