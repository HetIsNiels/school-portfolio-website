<?php
namespace portfolio\database;

use PDO;
use portfolio\Portfolio;

class Connection {
	/**
	 * @var Portfolio
	 */
	private $portfolio;

	/**
	 * @var PDO
	 */
	private $pdo;

	/**
	 * @param Portfolio $portfolio
	 */
	public function __construct($portfolio){
		$this->portfolio = $portfolio;

		$this->connect();
	}

	/**
	 * @throws DatabaseException
	 */
	private function connect(){
		try{
			$this->pdo = new PDO(
				$this->portfolio->getSetting('database.dsn', 'mysql:host=localhost;dbname=database'),
				$this->portfolio->getSetting('database.user', 'root'),
				$this->portfolio->getSetting('database.password', ''));
		}catch (\PDOException $e){
			throw new DatabaseException($e->getMessage());
		}
	}

	/**
	 * @param String $statement
	 * @return PreparedStatement
	 */
	public function prepare($statement){
		return new PreparedStatement($this, $statement);
	}

	/**
	 * @param string|null $name
	 *
	 * @return int
	 */
	public function lastId($name = null){
		return $this->pdo->lastInsertId($name);
	}

	/**
	 * @return PDO
	 */
	public function getPDO(){
		return $this->pdo;
	}
}