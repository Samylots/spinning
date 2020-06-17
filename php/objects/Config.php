<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 17:48
 */
class Config
{
	//public static $domain = 'localhost';
	public static $title = "Spinning de Beauce";
	public static $lang = "fr";
	//Database config
	public static $DBHost = "localhost";
	public static $DBName = "spinning";
	public static $DBUser = "root";
	public static $DBPassword = "";
	//Session and cookie vars names
	public static $userNameConfig = 'spinningUserName';
	public static $userPasswordConfig = 'spinningUserPassword';
	public static $userLoggedTimeConfig = 'spinningUserLoggedTime';
	public static $administratorTypeId = 3;
	public static $coachTypeId = 2;
	public static $usePageRestrictions = true;
	//public static $debugDatabase = true;
}