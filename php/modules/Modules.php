<?php

/**
 * Created by PhpStorm.
 * User: 436
 * Date: 2016-03-18
 * Time: 14:19
 */
if(!FUNCTIONS_LOADED){
	include_once( '../functions.php' );
}

abstract class Modules implements ModuleFormatter
{
	protected $table;
	public $adminToolbar;
	public $toolbar;

	/**
	 * Module constructor.
	 */
	public function __construct( $table ){
		$this->table = $table;
		$this->adminToolbar = new Toolbar();
		$this->toolbar = new Toolbar();
	}

	/**
	 * @param $function
	 * @return PDOStatement
	 */
	public static function call( $function ){
		return DB::getInstance()->call($function);
	}

	/**
	 * @return PDOStatement
	 */
	public function getAll(){
		try {
			return DB::getInstance()->select($this->table, '*', null);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	/**
	 * @param $id
	 * @param bool $usingActive
	 * @param bool $debug
	 * @return null|PDOStatement
	 */
	public function getOne( $id,$usingActive = true,  $debug = false ){
		try {
			return DB::getInstance()->select($this->table, '*', 'id='. $id, $usingActive, $debug);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getLast(){
		return DB::getInstance()->getLast();
	}

	public function add( $data ){
		try {
			return DB::getInstance()->add($this->table, $data);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function edit( $id, $data, $debug = false){
		try {
			return DB::getInstance()->update($id, $this->table, $data, "",$debug);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function delete( $id ){
		try {
			return DB::getInstance()->delete($id, $this->table);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function deepDelete( $id ){
		try {
			//this won't only set active to false, this actually delete if from the DB
			return DB::getInstance()->deepDelete($id, $this->table);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	protected static function testRequest(){
		if(!isPostRequest()){
			fail();
		}
	}

	public function getToolbars(){
		$user = new User();
		if($user->isAdmin()){
			$this->adminToolbar->getBar();
		}
		$this->toolbar->getBar();
	}
}