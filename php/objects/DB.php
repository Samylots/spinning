<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:57
 */
class DB
{
	private $scriptToCreateDatabase = 'init/createDb.sql';
	private $scriptToCreateDatabaseFromModules = '../../init/createDb.sql';
	private $databaseName;
	private $serverHost;
	private $serverLogin;
	private $serverPassword;
	private $serverOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND=> "SET NAMES utf8", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
	private $cnn;
	private static $_instance = null;

	/**
	 * Constructeur de la classe
	 *
	 * @param void
	 * @return void
	 */
	private function __construct() {
		$this->databaseName = Config::$DBName;
		$this->serverHost = Config::$DBHost;
		$this->serverLogin = Config::$DBUser;
		$this->serverPassword = Config::$DBPassword;
		try	{
			$this->cnn = $this->createConnection();
		} catch (PDOException $e){
			echo $e->getCode();
			if($e->getCode() == 1049 || $e->getCode()){
				$this->initDatabase();
			}else{echo $e->getCode() . ' In construct';
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
			}
		}
	}
	
	public function testDB(){
		//only to activate the DB object to create the DB if not exist
	}

	private function initDatabase(){
		var_dump($this->scriptToCreateDatabase);
		var_dump($this->scriptToCreateDatabaseFromModules);
		if(file_exists($this->scriptToCreateDatabase)){
			$pdo = new PDO("mysql:host=$this->serverHost", $this->serverLogin, $this->serverPassword, $this->serverOptions);
			$pdo->exec("drop database if exists " . $this->databaseName);
			$pdo->exec("create database if not exists " . $this->databaseName);
			$pdo->exec("use " . $this->databaseName);
			$init = new Initializer($pdo);
			$init->runScript($this->scriptToCreateDatabase);
			$this->cnn = $this->createConnection();
		}elseif(file_exists($this->scriptToCreateDatabaseFromModules)){
			$pdo = new PDO("mysql:host=$this->serverHost", $this->serverLogin, $this->serverPassword, $this->serverOptions);
			$pdo->exec("drop database if exists " . $this->databaseName);
			$pdo->exec("create database if not exists " . $this->databaseName);
			$pdo->exec("use " . $this->databaseName);
			$init = new Initializer($pdo);
			$init->runScript($this->scriptToCreateDatabaseFromModules);
			$this->cnn = $this->createConnection();
		}{
			var_dump('FILE DONT EXIST TO CREATE DB');
			var_dump(realpath($this->scriptToCreateDatabase));
		}
	}

	private function createConnection(){
		return new PDO("mysql:host=$this->serverHost;dbname=$this->databaseName",$this->serverLogin,$this->serverPassword, $this->serverOptions);
	}

	/**
	 * @return DB
	 */
	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new DB();
		}
		return self::$_instance;
	}

	/**
	 * @param $query
	 * @return PDOStatement
	 */
	public function customQuery($query, $debug = false){
		try	{
			if($debug){
				var_dump($query);
				echo $query;
			}
			return $this->cnn->query($query);
		} catch (PDOException $e){
			if($e->getCode() == "42S02"){
				$this->initDatabase();
				if($debug){
					var_dump($query);
					echo $query;
				}
				return $this->cnn->query($query);
			}else{echo($e->getCode() . ' In customQuery');
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
				return null;
			}
		}
	}

	/**
	 * @param $query
	 * @return integer
	 */
	public function exec($query, $debug = false){
		if($debug){
			var_dump($query);
		}
		try	{
			$result = ($this->cnn->exec($query) >= 1);
			if(!$result){
				fail($query);
			}
			return $result;
		} catch (PDOException $e){
			if($e->getCode() == "42S02"){
				$this->initDatabase();
				$result = ($this->cnn->exec($query) >= 1);
				if(!$result){
					fail($query);
				}
				return $result;
			}else{echo($e->getCode() . ' In exec');
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
				return null;
			}
		}
	}

	public function getLast(){
		return $this->cnn->lastInsertId();
	}

	public function add($table, $data){
		$queryString = "INSERT INTO ". $table ."(";
		$fields = [];
		$values = [];
		foreach($data as $key => $value){
			if($this->getValue($value) != false){
				$fields[] = $key;
				$values[] = $this->getValue($value);
			}
		}
		$queryString .= implode(", ", $fields) . ")";
		$queryString .= " VALUES(". implode(", ", $values) . ")";
		try	{
			$result = ($this->cnn->exec($queryString) >= 1);
			if(!$result){
				fail();
			}
			return $result;
		} catch (PDOException $e){
			if($e->getCode() == "42S02"){
				$this->initDatabase();
				$result = ($this->cnn->exec($queryString) >= 1);
				if(!$result){
					fail();
				}
				return $result;
			}else{echo($e->getCode() . ' In add');
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
				return null;
			}
		}
	}

	/**
	 * @param $conditions: string like "field = value AND [...]"
	 */
	public function select($table, $fields, $conditions = null, $usingActive = true, $debug = false){
		$queryString = 'SELECT ' . $fields;
		$queryString .= ' FROM ' . $table;

		$testing = substr(strtolower($conditions),0,8);
		$isGrouping = ($testing == "order by");

		$queryString .= ($conditions ? ($usingActive ? ' WHERE active=true ' : '') . ($isGrouping ? '' : ($usingActive ? ' AND ' : ' WHERE ')).
			$conditions  : ($usingActive ? ' WHERE active=true' : ''));
		if($debug){
			var_dump($queryString);
		}
		try	{
			return $this->cnn->query($queryString);
		} catch (PDOException $e){
			if($e->getCode() == "42S02"){
				$this->initDatabase();
				return $this->cnn->query($queryString);
			}else{echo($e->getCode() . ' In select');
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
				return null;
			}
		}
	}

	public function delete($id, $table){
		$queryString = 'UPDATE ' . $table. ' SET active=false WHERE id=' . $id;
		try	{
			return ($this->cnn->exec($queryString) == 1);
		} catch (PDOException $e){
			if($e->getCode() == "42S02"){
				$this->initDatabase();
				return ($this->cnn->exec($queryString) == 1);
			}else{echo $e->getCode() . ' In delete';
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
				return null;
			}
		}
	}

	public function deepDelete($id, $table){
		$queryString = 'DELETE FROM ' . $table. ' WHERE id=' . $id;
		try	{
			return ($this->cnn->exec($queryString) == 1);
		} catch (PDOException $e){
			if($e->getCode() == "42S02"){
				$this->initDatabase();
				return ($this->cnn->exec($queryString) == 1);
			}else{echo($e->getCode() . ' In deepDelete');
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
				return null;
			}
		}
	}

	/**
	 * @param $id: id for item to update
	 * @param $data: [ 'field' => 'value', 'field' => 'value' ]
	 * @param $conditions: string like "field = value AND [...]" OPTIONAL because id is required
	 */
	public function update($id, $table, $data, $conditions = "", $debug = false){
		$queryString = $this->getUpdateBaseString($table,$data);
		$queryString .= ' WHERE ' . ($conditions ? $conditions . ' AND id='. $id : 'id=' . $id );
		if($debug){
			var_dump($queryString);
		}
		try	{
			$result = $this->cnn->exec($queryString);
			if($result !== 1 && $result !== 0){
				fail();
			}
			return $result;
		} catch (PDOException $e){
			if($e->getCode() == "42S02"){
				$this->initDatabase();
				$result = $this->cnn->exec($queryString);
				if($result !== 1 && $result !== 0){
					fail();
				}
				return $result;
			}else{
				echo($e->getCode() . ' In update');
				echo "Erreur de la connexion MySQL -> {$e->getMessage()}";
				return null;
			}
		}
	}

	public function getValue($value, $debug = false){
		if($debug){
			var_dump($value);
		}
		$valueParsed = null;
		$jsDateTS = strtotime($value);
		if ($jsDateTS !== false){
			$valueParsed =  "'" . date('Y-m-d', strtotime(str_replace('-', '/', $value))) . "'";
		}
		if($value === 'true' || $value === 'false'){
			return $value == 'true' ? 1 : 0;
		}
		if(gettype($value) == "boolean"){
			$valueParsed =  $value;
		}
		if($value == 'null' && gettype($value) != "boolean"){
			$value = null;
		}
		if(gettype($value) == "string"){
			$valueParsed =  $this->cnn->quote($value);
		}
		if(gettype($value) == "integer" || gettype($value) == "double"){
			$valueParsed = $value;
		}
		//HAVE TO BE THE LAST TEST
		if($valueParsed == ''){
			if($debug){
				var_dump('Empty false thing');
				var_dump(gettype($value));
			}
			$valueParsed = false;
		}
		if($debug){
			var_dump($valueParsed);
		}
		return $valueParsed;
	}

	/**
	 * @param $table string
	 * @param $data array
	 * @return string (Base string for UPDATE query without WHERE clause)
	 */
	public function getUpdateBaseString($table, $data){
		$queryString = 'UPDATE ' . $table;
		$sets = [];
		foreach( $data as $key => $value ){
			if($this->getValue($value) != false && $this->getValue($value) != "''"){
				$sets[] = $key . '=' . $this->getValue($value);
			}else{
				$sets[] = $key . '=' . 'null';
			}
		}
		$queryString .= ' SET '. implode(', ', $sets);
		return $queryString;
	}

}