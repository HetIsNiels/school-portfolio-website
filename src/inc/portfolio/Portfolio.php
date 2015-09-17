<?php
namespace portfolio;

use portfolio\database\Connection;

class Portfolio {
	/**
	 * @var Connection $database
	 */
	private $database;

	/**
	 * @var UserManager $userManager
	 */
	private $userManager;

	/**
	 * @var PageManager pageManager
	 */
	private $pageManager;

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * @param array $settings
	 */
	public function __construct($settings){
		$this->settings = $settings;

		$this->install();
	}

	/**
	 * @return Connection
	 */
	public function getDatabase(){
		if(!isset($this->database))
			$this->database = new Connection($this);

		return $this->database;
	}

	/**
	 * @return UserManager
	 */
	public function getUserManager(){
		if(!isset($this->userManager))
			$this->userManager = new UserManager($this);

		return $this->userManager;
	}

	/**
	 * @return PageManager
	 */
	public function getPageManager(){
		if(!isset($this->pageManager))
			$this->pageManager = new PageManager($this);

		return $this->pageManager;
	}

	/**
	 * @param string $setting
	 * @param mixed $default
	 * @return mixed
	 */
	public function getSetting($setting, $default){
		$settingsPart = $this->settings;

		foreach(explode('.', $setting) as $part)
			$settingsPart = (isset($settingsPart[$part]) ? $settingsPart[$part] : $default);

		return $settingsPart;
	}

	/**
	 *
	 */
	public function install(){
		$tables = [
			'users' => [
				'columns' => [
					'id' => 'INTEGER PRIMARY KEY ASC',
					'username' => 'VARCHAR(225) NOT NULL',
					'password' => 'VARCHAR(225) NOT NULL'
				],

				'sql' => [
					'INSERT INTO users (username, password) VALUES (:username, :password)' => [
						'username' => 'admin',
						'password' => $this->getUserManager()->encryptPass('admin')
					]
				]
			],

			'sessions' => [
				'columns' => [
					'id' => 'INTEGER PRIMARY KEY ASC',
					'user' => 'int DEFAULT 0 NOT NULL',
					'ip' => 'VARCHAR(225) NOT NULL',
					'authCode7' => 'VARCHAR(225) NOT NULL',
					'creation_date' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL',
					'expired' => 'INTEGER DEFAULT 0 NOT NULL'
				]
			],

			'pages' => [
				'columns' => [
					'id' => 'INTEGER PRIMARY KEY ASC',
					'url' => 'VARCHAR(225) NOT NULL',
					'caption' => 'VARCHAR(225) NOT NULL',
					'visible' => 'int DEFAULT 0 NOT NULL'
				]
			],

			'menu' => [
				'columns' => [
					'id' => 'INTEGER PRIMARY KEY ASC',
					'page' => 'INTEGER NOT NULL',
					'caption' => 'VARCHAR(225) NOT NULL',
					'visible' => 'INTEGER DEFAULT 0 NOT NULL',
					'sequence' => 'INTEGER NOT NULL'
				]
			],

			'elements' => [
				'columns' => [
					'id' => 'INTEGER PRIMARY KEY ASC',
					'page' => 'INTEGER NOT NULL',
					'parent' => 'INTEGER NOT NULL',
					'type' => 'VARCHAR(225) NOT NULL',
					'data' => 'TEXT NOT NULL',
					'visible' => 'INTEGER DEFAULT 0 NOT NULL',
					'sequence' => 'INTEGER NOT NULL'
				]
			]
		];

		$result = $this->getDatabase()->prepare('SELECT name FROM sqlite_master WHERE type = "table"')->executeQuery();

		while($result->next())
			$tables[$result->getString('name')]['found'] = true;

		foreach($tables as $table => $data){
			if(!isset($data['found']) || $data['found'] == false){
				$sql = 'CREATE TABLE ' . $table . '(';

				foreach($data['columns'] as $column => $value){
					$sql .= $column . ' ' . $value . ',';
				}

				$sql = substr($sql, 0, strlen($sql) - 1);

				$sql .= ')';

				$this->getDatabase()->prepare($sql)->execute();

				if(isset($data['sql'])) {
					foreach ($data['sql'] as $sql => $params) {
						$statement = $this->getDatabase()->prepare($sql);

						foreach ($params as $key => $val) {
							$statement->setParameter($key, $val);
						}

						$statement->execute();
					}
				}
			}
		}
	}
} 