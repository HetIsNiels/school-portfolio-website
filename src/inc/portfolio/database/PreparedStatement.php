<?php
namespace portfolio\database;

class PreparedStatement {
	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @var String
	 */
	private $statement;

	/**
	 * @var array
	 */
	private $parameters;

	/**
	 * @param Connection $connection
	 * @param String $statement
	 */
	public function __construct(Connection $connection, $statement){
		$this->connection = $connection;
		$this->statement = $statement;

		$this->parameters = array();
	}

	/**
	 * @param String $name
	 * @param mixed $value
	 * @return $this
	 */
	public function setParameter($name, $value){
		$this->parameters[$name] = $value;

		return $this;
	}

	/**
	 * @return bool
	 * @throws DatabaseException
	 */
	public function execute(){
		$preparedStatement = $this->connection->getPDO()->prepare($this->statement);


		if($preparedStatement === false)
			throw new DatabaseException('SQL error: ' . $this->connection->getPDO()->errorInfo()[2] . ' - SQL was: ' . $this->statement);

		$result = $preparedStatement->execute($this->parameters);

		if($result !== false)
			return $result;

		throw new DatabaseException('SQL error: ' . $preparedStatement->errorInfo()[2] . ' - SQL was: ' . $this->statement);
	}

	/**
	 * @return ResultSet
	 * @throws DatabaseException
	 */
	public function executeQuery(){
		$preparedStatement = $this->connection->getPDO()->prepare($this->statement);

		if($preparedStatement === false)
			throw new DatabaseException('SQL error: ' . $this->connection->getPDO()->errorInfo()[2] . ' - SQL was: ' . $this->statement);

		if($preparedStatement->execute($this->parameters) !== false)
			return new ResultSet($preparedStatement->fetchAll());

		throw new DatabaseException('SQL error: ' . $preparedStatement->errorInfo()[2] . ' - SQL was: ' . $this->statement);
	}
}