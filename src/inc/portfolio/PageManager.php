<?php
namespace portfolio;

class PageManager {
	/**
	 * @var Portfolio
	 */
	private $portfolio;

	/**
	 * @param Portfolio $portfolio
	 */
	public function __construct($portfolio)
	{
		$this->portfolio = $portfolio;
	}

	/**
	 * @param string $url
	 * @param string $caption
	 *
	 * @return bool
	 */
	public function create($url, $caption)
	{
		if($this->pageExists($url))
			return false;

		return $this->portfolio->getDatabase()->prepare('INSERT INTO pages (url, caption, visible) VALUES (:url, :caption, 1)')
			->setParameter('url', $url)
			->setParameter('caption', $caption)
			->execute();
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	public function pageExists($url)
	{
		return $this->portfolio->getDatabase()->prepare('SELECT 1 FROM pages WHERE url = :url AND visible = 1')->setParameter('url', $url)->executeQuery()->next();
	}

	/**
	 * @param int $page
	 * @param string $url
	 * @param string $caption
	 *
	 * @return bool
	 */
	public function modify($page, $url, $caption)
	{
		if($this->pageExists($url))
			return false;

		return $this->portfolio->getDatabase()->prepare('UPDATE pages SET url = :url, caption = :caption WHERE id = :id AND visible = 1')
			->setParameter('url', $url)
			->setParameter('caption', $caption)
			->setParameter('id', $page)
			->execute();
	}

	/**
	 * @param string|int $id
	 *
	 * @return array|null
	 */
	public function getPage($id)
	{
		if(is_numeric($id))
			$page = $this->portfolio->getDatabase()->prepare('SELECT * FROM pages WHERE id = :id AND visible = 1')->setParameter('id', $id)->executeQuery();
		else
			$page = $this->portfolio->getDatabase()->prepare('SELECT * FROM pages WHERE url = :url AND visible = 1')->setParameter('url', $id)->executeQuery();

		if(!$page->next())
			return null;

		$result = [];

		$result['id'] = $page->getInt('id');
		$result['caption'] = $page->getString('caption');
		$result['url'] = $page->getString('url');
		/*
		 * [{"id": 0, "type": "box", "children": [{"id": 1, "type": "heading", "values": {"text": "Pagina niet gevonden!"}}, {"id": 2, "type": "inner", "values": {"text": "De opgevraagde pagina is niet gevonden."}}]}]
		 */
		$result['content'] = $this->getElements($page->getInt('id'), -1);

		return $result;
	}

	/**
	 * @param int $page
	 * @param int $parent
	 *
	 * @return array
	 */
	private function getElements($page, $parent)
	{
		$elements = $this->portfolio->getDatabase()->prepare('SELECT * FROM elements WHERE page = :page AND parent = :parent AND visible = 1 ORDER BY sequence')
			->setParameter('page', $page)
			->setParameter('parent', $parent)
			->executeQuery();

		$result = [];

		while($elements->next()){
			$element = [];
			$element['id'] = $elements->getInt('id');
			$element['type'] = $elements->getString('type');
			$element['values'] = json_decode($elements->getString('data'));
			$element['children'] = $this->getElements($page, $elements->getInt('id'));

			$result[] = $element;
		}

		return $result;
	}

	/**
	 * @param int $page
	 * @param int $parent
	 * @param string $type
	 * @param array $values
	 * @param int $sequence
	 *
	 * @return array|false
	 */
	public function createElement($page, $parent, $type, $values, $sequence)
	{
		$element = $this->portfolio->getDatabase()->prepare('INSERT INTO elements (page, parent, type, data, sequence, visible) VALUES (:page, :parent, :type, :data, :sequence, 1)')
			->setParameter('page', $page)
			->setParameter('parent', $parent)
			->setParameter('type', $type)
			->setParameter('data', json_encode($values))
			->setParameter('sequence', $sequence)
			->execute();

		if($element == false)
			return false;

		$result = [
			'element' => [
				'id' => $this->portfolio->getDatabase()->lastId(),
				'type' => $type,
				'values' => $values,
				'children' => []
			]
		];

		return $result;
	}

	/**
	 * @param int $page
	 * @param int $element
	 *
	 * @return bool
	 */
	public function deleteElement($page, $element)
	{
		$elements = $this->getElements($page, $element);

		foreach($elements as $elm)
			$this->deleteElement($page, $elm['id']);

		return $this->portfolio->getDatabase()->prepare('DELETE FROM elements WHERE page = :page AND id = :id')
			->setParameter('page', $page)
			->setParameter('id', $element)
			->execute();
	}

	/**
	 * @param int $page
	 * @param int $element
	 * @param array $values
	 * @param int $sequence
	 *
	 * @return bool
	 */
	public function modifyElement($page, $element, $values, $sequence)
	{
		return $this->portfolio->getDatabase()->prepare('UPDATE elements SET data = :data, sequence = :sequence WHERE page = :page AND id = :id')
			->setParameter('data', json_encode($values))
			->setParameter('sequence', $sequence)
			->setParameter('page', $page)
			->setParameter('id', $element)
			->execute();
	}

	/**
	 * @return array
	 */
	public function listPages(){
		$pages = $this->portfolio->getDatabase()->prepare('SELECT * FROM pages WHERE visible = 1')
			->executeQuery();

		$result = [];

		while($pages->next()){
			$page = [];
			$page['id'] = $pages->getInt('id');
			$page['url'] = $pages->getString('url');
			$page['caption'] = $pages->getString('caption');

			$result[] = $page;
		}

		return $result;
	}

	/**
	 * @param int $page
	 *
	 * @return bool
	 */
	public function delete($page)
	{
		return $this->portfolio->getDatabase()->prepare('UPDATE pages SET visible = 0 WHERE id = :page')
			->setParameter('page', $page)
			->execute();
	}
}