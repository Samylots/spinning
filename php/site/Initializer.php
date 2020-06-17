<?php

/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 25/03/2016
 * Time: 13:06
 */
class Initializer
{
	private $cnn;

	/**
	 * Initializer constructor.
	 * @param PDO $pdo
	 */
	public function __construct($pdo){
		$this->cnn = $pdo;
	}

	public function runScript($file){
		// read the sql file
		$f = fopen($file, "r+");
		$sqlFile = fread($f, filesize($file));
		$sqlArray = explode(';',$sqlFile);
		foreach ($sqlArray as $stmt) {
			if (strlen($stmt)>3 && substr(ltrim($stmt),0,2)!='/*') {
				try {
					$this->cnn->exec($stmt);
				}catch(PDOException $ex){
					fail("Couldnt run {$file} correctly at: '{$stmt}' because of {$ex->getMessage()}");
					$this->cnn->exec("drop database");
					break;
				}
			}
		}
	}

}