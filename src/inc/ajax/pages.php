<?php
require_once __DIR__ . '/../bootstrap.php';

$result = [];

$post = [
	'type' => (isset($_POST['type']) ? $_POST['type'] : 'login'),
	'caption' => (isset($_POST['caption']) ? $_POST['caption'] : ''),
	'url' => urlencode(isset($_POST['url']) ? $_POST['url'] : ''),
	'page' => (isset($_POST['page']) ? $_POST['page'] : ''),
	'parent' => (isset($_POST['parent']) ? $_POST['parent'] : ''),
	'element-type' => (isset($_POST['element-type']) ? $_POST['element-type'] : ''),
	'values' => (isset($_POST['values']) ? $_POST['values'] : ''),
	'sequence' => (isset($_POST['sequence']) ? $_POST['sequence'] : ''),
	'element' => (isset($_POST['element']) ? $_POST['element'] : '')
];

$result['id'] = 0;
$result['url'] = $post['url'];
$result['caption'] = $post['caption'];
$result['element'] = [];
$result['code'] = 0;
$result['msg'] = '';

if($post['type'] == 'request') {
	$result = $portfolio->getPageManager()->getPage($post['url']);
	if($result == null)
		$result = ['code' => 0, 'url' => 'index'];
	else
		$result['code'] = 1;
}

if($portfolio->getUserManager()->verifyAuthCode7((isset($_COOKIE['authCode7']) ? $_COOKIE['authCode7'] : ''))) {
	if ($post['type'] == 'create') {
		$result['code'] = ($portfolio->getPageManager()->create($post['url'], $post['caption']) == true ? 1 : 0);
		$result['msg'] = ($result['code'] == 1 ? 'De pagina is aangemaakt en geopend.' : 'De pagina kon niet worden aangemaakt. Waarschijnlijk bestaat deze al.');
	} elseif ($post['type'] == 'modify') {
		$result['code'] = ($portfolio->getPageManager()->modify($post['page'], $post['url'], $post['caption']) == true ? 1 : 0);
		$result['msg'] = ($result['code'] == 1 ? 'De pagina instellingen zijn gewijzigd!' : 'De pagina kon niet worden gewijzigd.');
	} elseif ($post['type'] == 'delete') {
		$result['code'] = ($portfolio->getPageManager()->delete($post['page']) == true ? 1 : 0);
		$result['msg'] = ($result['code'] == 1 ? 'De pagina is verwijderd!' : 'De pagina kon niet worden verwijderd.');
	} elseif ($post['type'] == 'list') {
		$result['pages'] = $portfolio->getPageManager()->listPages();
		$result['code'] = 1;
	} elseif ($post['type'] == 'create-element') {
		$result = $portfolio->getPageManager()->createElement($post['page'], $post['parent'], $post['element-type'], $post['values'], $post['sequence']);

		if ($result == null) {
			$result['code'] = 0;
			$result['msg'] = 'Element kon niet worden toegevoegd.';
		} else {
			$result['code'] = 1;
		}
	} elseif ($post['type'] == 'delete-element') {
		$result['code'] = ($portfolio->getPageManager()->deleteElement($post['page'], $post['element']) == true ? 1 : 0);
		$result['msg'] = ($result['code'] == 0 ? 'Element kon niet worden verwijderd.' : '');
	} elseif ($post['type'] == 'modify-element') {
		$result['code'] = ($portfolio->getPageManager()->modifyElement($post['page'], $post['element'], $post['values'], $post['sequence']) == true ? 1 : 0);
		$result['msg'] = ($result['code'] == 0 ? 'Element kon niet worden gewijzigd.' : '');
		$result['p'] = $post;
	}
}

header('content-type: application/json');
echo json_encode($result);