<?php
namespace portfolio;

class UserManager {
	/**
	 * @var Portfolio
	 */
	private $portfolio;

	/**
	 * @param Portfolio $portfolio
	 */
	public function __construct($portfolio){
		$this->portfolio = $portfolio;
	}

	/**
	 * @param string $username
	 * @param string $password
	 */
	public function tryLogin($username, $password){
		$result = [];

		$result['code'] = 0;
		$result['msg'] = '';
		$result['authCode7'] = '';
		$result['editorSource'] = '';

		if(isset($_COOKIE['authCode7'])){
			$user = $this->verifyAuthCode7($_COOKIE['authCode7']);

			if($user !== false){
				$result['code'] = 3;
				$result['authCode7'] = $_COOKIE['authCode7'];
				$result['editorSource'] = $this->portfolio->getSetting('web.url', '') . 'assets/js/compiler.php?authCode7=' . $result['authCode7'];

				return $result;
			}
		}

		$resultSet = $this->portfolio->getDatabase()->prepare('SELECT * FROM users WHERE username = :username AND password = :password')
			->setParameter('username', $username)
			->setParameter('password', $this->encryptPass($password))
			->executeQuery();

		if ($resultSet->next()) {
			$result['code'] = 1;
			$result['authCode7'] = $this->getAuthCode7($resultSet->getInt('id'));
			$result['editorSource'] = $this->portfolio->getSetting('web.url', '') . 'assets/js/compiler.php?authCode7=' . $result['authCode7'];
		} else {
			$result['code'] = 2;
			$result['msg'] = 'Deze combinatie van gebruikersnaam en wachtwoord is onjuist.';
		}

		return $result;
	}

	/**
	 * @param string $password
	 * @return string
	 */
	public function encryptPass($password){
		return hash('sha512', $password);
	}

	/**
	 * @param int $user
	 *
	 * @return string
	 */
	private function getAuthCode7($user){
		$authCode7 = hash('sha512', hash('sha256', $this->encryptPass($user)) . microtime(true));
		setcookie('authCode7', $authCode7, strtotime('+' . $this->portfolio->getSetting('auth.sessionLength', '1 day')), '/');

		$this->portfolio->getDatabase()->prepare('INSERT INTO sessions (user, ip, authCode7) VALUES (:user, :ip, :authCode7)')
			->setParameter('user', $user)
			->setParameter('ip', $_SERVER['REMOTE_ADDR'])
			->setParameter('authCode7', $authCode7)
			->execute();

		return $authCode7;
	}

	/*
	 * @param string $authCode7
	 *
	 * @return int|bool
	 */
	public function verifyAuthCode7($authCode7){
		$resultSet = $this->portfolio->getDatabase()->prepare('SELECT id, user, creation_date FROM sessions WHERE authCode7 = :authCode7 AND ip = :ip AND expired = "0"')
			->setParameter('authCode7', $authCode7)
			->setParameter('ip', $_SERVER['REMOTE_ADDR'])
			->executeQuery();

		if($resultSet->next()) {
			$time = strtotime($resultSet->getString('creation_date'));

			if($time >= strtotime('-' . $this->portfolio->getSetting('auth.sessionLength', '1 day')))
				return $resultSet->getInt('user');

			$this->portfolio->getDatabase()->prepare('UPDATE sessions SET expired = "1" WHERE id = :id')
				->setParameter('id', $resultSet->getInt('id'))
				->execute();

			return false;
		}else
			return false;
	}

	public function lookupActiveSession(){
		$result = [];

		$result['code'] = 0;
		$result['authCode7'] = '';
		$result['editorSource'] = '';

		if(isset($_COOKIE['authCode7'])){
			$user = $this->verifyAuthCode7($_COOKIE['authCode7']);

			if($user !== false){
				$result['code'] = 1;
				$result['authCode7'] = $_COOKIE['authCode7'];
				$result['editorSource'] = $this->portfolio->getSetting('web.url', '') . 'assets/js/compiler.php?authCode7=' . $result['authCode7'];

				return $result;
			}
		}

		return $result;
	}

	/**
	 * @param string $authCode7
	 *
	 */
	public function logout($authCode7){
		$this->portfolio->getDatabase()->prepare('UPDATE sessions SET expired = "1" WHERE authCode7 = :authCode7 AND ip = :ip')
			->setParameter('authCode7', $authCode7)
			->setParameter('ip', $_SERVER['REMOTE_ADDR'])
			->execute();
	}

	/**
	 * @param string $authCode7
	 * @param string $username
	 * @param string $password
	 * @param string $passwordCurrent
	 */
	public function modify($authCode7, $username, $password, $passwordCurrent){
		$result = [];

		$result['code'] = 0;
		$result['msg'] = '';

		$user = $this->verifyAuthCode7($authCode7);

		if($user === false){
			$result['code'] = 1;
			$result['msg'] = 'Invalid authCode7';
			return $result;
		}

		$resultSet = $this->portfolio->getDatabase()->prepare('SELECT 1 FROM users WHERE id = :id AND password = :password')->setParameter('id', $user)->setParameter('password', $this->encryptPass($passwordCurrent))->executeQuery();

		if(!$resultSet->next()){
			$result['code'] = 2;
			$result['msg'] = 'Uw huidige wachtwoord komt niet overeen.';
			return $result;
		}

		if(!empty($username) && strlen($username) >= 3)
			$this->portfolio->getDatabase()->prepare('UPDATE users SET username = :username WHERE id = :id')->setParameter('username', $username)->setParameter('id', $user)->execute();

		if(!empty($password) && strlen($password) >= 3)
			$this->portfolio->getDatabase()->prepare('UPDATE users SET password = :password WHERE id = :id')->setParameter('password', $this->encryptPass($password))->setParameter('id', $user)->execute();

		$result['code'] = 3;
		$result['msg'] = 'Uw gegevens zijn gewijzigd.';

		if(empty($username) && empty($password) && strlen($username) < 3 && strlen($password) < 3){
			$result['code'] = 4;
			$result['msg'] = 'Er is niks gewijzigd.';
		}

		return $result;
	}
} 