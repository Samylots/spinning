<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 14:27
 */

class User
{
	private $user = null;
	private $restrictedPages =[];
	private $allowedPages =[];
	private $isLogged;

	function __construct() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		if(Config::$usePageRestrictions){
			$this->restrict('administration.php',Config::$administratorTypeId);
			$this->restrict('administrationSessions.php',Config::$administratorTypeId);
			$this->restrict('administrationSessionWeeks.php',Config::$administratorTypeId);
		}
		$this->tryToLogIn(); //CHeck if can log in user
		// call pages restrictions
		$this->checkRestrictions();
	}

	public function login($username, $password){
		return $this->testCredentials($username, $password);
	}

	private function testCredentials($username, $password, $isAutoLogin = false){
		$members = new Members();
		$user = DB::getInstance()->select('members','*','email=\''. $username .'\'')->fetch();
		// if logging manually by password input
		// OR if logging automatically
		// (has to be the same as in the database because i'm saving it in a session variable)
		if(password_verify($password, $user['password']) || ($isAutoLogin && ($password === $user['password']))){
			$this->user = $user;
			$_SESSION[Config::$userNameConfig] = $user['email'];
			$_SESSION[Config::$userPasswordConfig] = $user['password'];
			$_SESSION[Config::$userLoggedTimeConfig] = time();
			set_cookie(Config::$userNameConfig, $user['email']);
			set_cookie(Config::$userPasswordConfig, $user['password']);
			$members->edit($user['id'],[
				'last_activity' => now()
			]);
			$this->isLogged = true;
			return true;
		}
		return false;
	}

	public function logout(){
		session_unset();
		session_destroy();
		delete_cookie(Config::$userNameConfig);
		delete_cookie(Config::$userPasswordConfig);
	}

	public function isAdmin(){
		if($this->user['type_id'] == Config::$administratorTypeId){
			return true;
		}
		return false;
	}

	public function isLogged(){
		if($this->isLogged){
			return true;
		}
		return $this->tryToLogIn();
	}

	private function tryToLogIn(){
		if ( session(Config::$userNameConfig) && session(Config::$userPasswordConfig) ){
			return $this->testCredentials(session(Config::$userNameConfig), session(Config::$userPasswordConfig), true);
		}else if ( cookie(Config::$userNameConfig) && cookie(Config::$userPasswordConfig) ){
			return $this->testCredentials(cookie(Config::$userNameConfig), cookie(Config::$userPasswordConfig), true);
		}
		return false;
	}

	public function getUserId(){
		if($this->isLogged()){
			return $this->user['id'];
		}else{
			needToLogIn();
			return null;
		}
	}

	public function restrict($page, $rank){
		$restrictedPages = search($this->restrictedPages, 'page',$page);
		if(empty($restrictedPages)){
			$this->restrictedPages[] = [
				'id' => count($this->restrictedPages) +1,
				'page' => $page,
				'rank' => $rank
			];
		}
	}

	public function allow($page, $rank){
		$allowedPages = search($this->allowedPages, 'page',$page);
		if(empty($allowedPages)){
			$this->allowedPages[] = [
				'id' => count($this->allowedPages) +1,
				'page' => $page,
				'rank' => $rank
			];
		}

	}

	private function checkRestrictions(){
		$actualPage = basename($_SERVER['PHP_SELF']);
		$pages = search($this->allowedPages, 'page',$actualPage);
		if(empty($pages)){
			$pages = search($this->restrictedPages, 'page',$actualPage);
			if(!empty($pages)){
				$this->isCorrectRank($pages, $this->user['type_id']);
			}
		}else{
			$this->isCorrectRank($pages, $this->user['type_id']);
		}
	}

	private function isCorrectRank($pages, $rank){
		$isAllowed = false;
		if($this->user){
			foreach( $pages as $page ){
				if ( $page[ 'rank' ] == $rank ){
					$isAllowed = true;
				}
			}
		}
		if(!$isAllowed){
			redirect('restricted.php');
		}
	}

	public function getFullName(){
		return $this->user['firstname'] .' '. $this->user['lastname'];
	}

	public function getUser(){
		return $this->user;
	}
}