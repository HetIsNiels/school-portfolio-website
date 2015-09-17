<?php
namespace portfolio\database;

class ResultSet {
	/*
	 * array
	 */
	private $set;

	/**
	 * @var int
	 */
	private $position = -1;

	/**
	 * @param array $set
	 */
	public function __construct($set){
		$this->set = $set;
	}

	/**
	 * Get size of the result set
	 * @return int
	 */
	public function getSize(){
		return count($this->set);
	}

	/**
	 * Go to next item in result set
	 * @return bool
	 */
	public function next()
	{
		$this->position++;

		return $this->valid();
	}

	/**
	 * Check if current item exists
	 * @return bool
	 */
	public function valid()
	{
		return isset($this->set[$this->position]);
	}

	private function get($key){
		return $this->set[$this->position][$key];
	}

	/**
	 * @param $key
	 * @return string
	 */
	public function getString($key){
		return (string) $this->get($key);
	}

	/**
	 * @param $key
	 * @return int
	 */
	public function getInt($key){
		return (int) $this->get($key);
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function getBool($key){
		return (bool) $this->get($key);
	}

	/**
	 * @return array
	 */
	public function getSet()
	{
		return $this->set[$this->position];
	}
}