<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 14:31
 */
define('FUNCTIONS_LOADED', true);
define('DAY_STAMP', 60*60*24);
define('WEEK_STAMP', DAY_STAMP*7);
define('YEAR_STAMP' , 31536000);
date_default_timezone_set('America/Toronto');

function __autoload($class_name) {
	try {
		if (! @include_once( "objects/{$class_name}.php" )){ // @ - to suppress warnings,
			if (! @include_once( "site/{$class_name}.php" )){ // @ - to suppress warnings,
				include_once( "modules/{$class_name}.php" );
			}
		}
	}
	catch(Exception $e) {
		echo "Message : " . $e->getMessage();
		echo "Code : " . $e->getCode();
	}
}

function get($varName){
	$post = isset($_POST[$varName]) ? htmlentities($_POST[$varName]) : null;
	$get = isset($_GET[$varName]) ? htmlentities($_GET[$varName]) : null;

	$post = is_string($post) ? makeStringUTF8($post) : $post;
	$get = is_string($get) ? makeStringUTF8($get) : $get;
	return (isset($post) ? $post : (isset($get) ? $get : false));
}

function isCurrentRequest($page,$request = null, $debug = false){
	if($request != null){
		$currentRequest = basename($request);
	}else{
		$currentRequest = basename($_SERVER['REQUEST_URI']);
	}
	if(strpos($page, '*') !== false){
		$page = substr($page,0,strlen($page)-1);
		if($debug){
			var_dump( $currentRequest . ' VS ' . $page);
		}
		if(strpos($currentRequest, $page) !== false){
			return true;
		}
	}
	if($debug){
		var_dump( $currentRequest . ' VS ' . $page);
	}
	if ($page === $currentRequest) {
		return true;
	}
}
/*
 * Problem:
 * I was getting trouble with accents where they were being converted to things like : &eacute;
 * And the trouble is in SQL, the closing bracket is ";" so this was closing the query early than supposed...
 * Solution:
 * http://stackoverflow.com/questions/5950818/error-in-encoding-mysql-how-can-i-reconvert-it-to-something-else
*/
function makeStringUTF8($data){
	if (is_string($data) === true){
		// has html entities?
		if (strpos($data, '&') !== false){
			// if so, revert back to normal
			$data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
		}
		// make sure it's UTF-8
		if (function_exists('iconv') === true){
			return @iconv('UTF-8', 'UTF-8//IGNORE', $data);
		}
		else if (function_exists('mb_convert_encoding') === true){
			return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
		}
		return utf8_encode(utf8_decode($data));
	}else if (is_array($data) === true){
		$result = array();

		foreach ($data as $key => $value){
			$result[makeStringUTF8($key)] = makeStringUTF8($value);
		}
		return $result;
	}
	return $data;
}

function session($varName){
	$sessionVar = isset($_SESSION[$varName]) ? $_SESSION[$varName] : null;
	if($sessionVar){
		return htmlentities($sessionVar);
	}
	return false;
}

function set_cookie($config, $value){
	setcookie( $config, $value, time() + (30* DAY_STAMP),'/', null ,null,true);
}

function delete_cookie($config){
	unset($_COOKIE[$config]);
	setcookie($config, null, -1, '/');
}

function cookie($varName){
	$sessionVar = isset($_COOKIE[$varName]) ? $_COOKIE[$varName] : null;
	if($sessionVar){
		return htmlentities($sessionVar);
	}
	return false;
}

function search($array, $key, $value){
	$results = array();
	if (is_array($array)) {
		if (isset($array[$key]) && $array[$key] == $value) {
			$results[] = $array;
		}

		foreach ($array as $subarray) {
			$results = array_merge($results, search($subarray, $key, $value));
		}
	}
	return $results;
}

function isInArray($array, $key, $value){
	return empty(search($array, $key, $value));
}

function orderBy( &$array, $on, $order=SORT_ASC)
{
	$new_array = array();
	$sortable_array = array();

	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}
		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
				break;
			case SORT_DESC:
				arsort($sortable_array);
				break;
		}
		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}
	$array = $new_array;
}

function redirect($location){
	header('Location: '. $location);
}

function plurialNoun($nb, $text){
	$words = explode(' ', $text);
	if($nb > 1){
		return $nb . " " . implode('s ',$words) . 's';
	}
	return $nb . " " . $text;
}

function plurialVerb($nb, $text){
	if($nb > 1){
		if(substr($text,-1) == 'e'){
			return $nb . " " . $text . 'nt';
		}else{
			return $nb . " " . $text . 'ent';
		}
	}
	return $nb . " " . $text;
}

function isPostRequest(){
	return ($_SERVER['REQUEST_METHOD'] === 'POST');
}

function isGetRequest(){
	return ($_SERVER['REQUEST_METHOD'] === 'GET');
}

function fail($error = 'ERROR: invalid action'){
	header('HTTP/1.1 500 Stopped request');
	header('Content-Type: application/json; charset=UTF-8');
	die($error);
}

function invalid($error = 'ERROR: invalid values'){
	header('HTTP/1.1 601 Stopped request');
	header('Content-Type: application/json; charset=UTF-8');
	die($error);
}

function alert($error){
	header('HTTP/1.1 602 Stopped request');
	header('Content-Type: application/json; charset=UTF-8');
	die($error);
}

function needToLogIn($error = 'Vous devez vous connecter afin d\'effectuer cette action!'){
	header('HTTP/1.1 603 Stopped request');
	header('Content-Type: application/json; charset=UTF-8');
	die($error);
}

function kill(){
	header('HTTP/1.1 604 Stopped request');
	header('Content-Type: application/json; charset=UTF-8');
	die();
}

function hasPosted($values, $debug = false){
	$hasPostedAll = true;
	if(is_array($values)){
		foreach( $values as $value ){
			if($debug){
				var_dump(get($value));
			}
			$hasPostedAll &= ( get($value) !== false );
		}
	}else{
		$hasPostedAll &= ( get($values) !== false );
	}
	$hasPostedAll &= isPostRequest();
	return $hasPostedAll;
}

function hasPushed($values, $debug = false){
	$hasPushedAll = true;
	if(is_array($values)){
		foreach( $values as $value ){
			if($debug){
				var_dump(get($value));
			}
			$hasPushedAll &= ( get($value) !== false );
		}
	}else{
		$hasPushedAll &= ( get($values) !== false );
	}
	$hasPushedAll &= isGetRequest();
	return $hasPushedAll;
}

function getIdName($title){
	return 'id="' . $title . '" name="' . $title . '" ';
}

function now(){
	return date('Y-m-d H:i:s');
}

function printDatas(){
	foreach($_POST as $key => $val){
		echo '<input id="'. $key .'"type="hidden" value="'. $val .'">';
	}
	foreach($_GET as $key => $val){
		echo '<input id="'. $key .'"type="hidden" value="'. $val .'">';
	}
}

function getTime($value){
	$date1 = DateTime::createFromFormat( 'H : i', $value);
	$date2 = DateTime::createFromFormat( 'H:i:s.u', $value);
	if($date1){
		return $date1->format('H:i:s');
	}

	if($date2){
		return $date2->format('H:i:s');
	}
	return false;
}

/**
 * @param $date
 * @return string
 */
function formatDateTime( $date, $toUpper = true, $showTime = false, $showDay = false){
	//Use "ucfirst" to capitalize first letter
	//Use "mb_strtoupper(str,'UTF-8')" to capitalize all letters and accents
	if($date == ''){
		return '---';
	}
	$Months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
	if(gettype($date) == 'string'){
		$date = new DateTime($date);
	}
	$weekDay = intval($date->format('N'));
	$weekDay = $weekDay == 7 ? 0: $weekDay;
	$dayTitle = Helper::$days[$weekDay];
	if($toUpper){
		$dayTitle = mb_strtoupper($dayTitle,'UTF-8');
		return ($showDay? $dayTitle . ' ': '') . intval($date->format('d')) . ' ' .
		mb_strtoupper($Months[ intval($date->format('m')) - 1 ], 'UTF-8') . ' ' . $date->format('Y') .
		($showTime? ' à '. $date->format('H:i') : '');
	}else{
		return ($showDay? $dayTitle  . ' ': '') .intval($date->format('d')) . ' ' .
		$Months[ intval($date->format('m')) - 1 ] . ' ' . $date->format('Y') . ($showTime? ' à '. $date->format('H:i') : '');
	}
}

function formatTime($time){
	if(gettype($time) == 'string'){
		$time = new DateTime($time);
	}
	$hours = intval($time->format('H'));
	$minutes = intval($time->format('i'));

	return ($hours ? plurialNoun($hours, 'heure') : '') . (($hours && $minutes) ? ' et ' : '') . ($minutes ? plurialNoun($minutes, 'minutes') : '');
}

function getFullMinutes($minutes){
	if($minutes < 10){
		return '0' . $minutes;
	}
	return $minutes;
}



function convertDate($JSTimeStamp){
	return gmdate('Y-m-d', $JSTimeStamp/1000);
}

function getNbOfDays($month, $year){
	return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

/**
 * Fonction qui permet de bien formater les nombre afait de les écrires
 * en tant que prix de la forme "XXX[$glue]XXX[$glue]XXX.XX[...(nomber of decimals)]$"
 * @param $value string
 * @param $decimals int
 * @param string $currency string
 * @param string $glue string
 * @return string
 */
function formatPrice($value, $decimals, $currency = '$', $glue = ' '){
	if($value == ''){
		return '---';
	}
	$decimalString = "";
	if(strpos($value,'.') >-1){
		//rounding the cents
		$decimalsValue = floatval(substr($value,strpos($value,'.'),$decimals+2));
		$decimalsValue = round($decimalsValue * pow(10,$decimals));
		$decimalString = '.' .$decimalsValue;
		$value = substr($value, 0, strlen($value) - abs(strpos($value,'.') - strlen($value)));
	}
	$groupOfThree = null;
	if(strlen($value) > 3){
		$groupOfThree = substr($value, strlen($value) - 3, 3);
		$rest = substr($value, 0, strlen($value) - 3);
	}else{
		$rest = $value;
	}
	if(strlen($rest) >3){
		//recursive to do groups of 3 numbers
		$rest = formatPrice($rest,$decimals).$glue;
	}
	return $rest . ($groupOfThree ? $glue . $groupOfThree : '') .
	($decimalString ? (strlen($decimalString) == $decimals +1 ? $decimalString  : $decimalString . '0') : '.00') . $currency;
}

function generateRandomSequence( $length = 8) {
	$chars = 'bdfghjkmnpqrstvwxzBCDFGHJKMNPRSTVWXYZ23456789'; //bank of letters
	$count = mb_strlen($chars);

	for ($i = 0, $result = ''; $i < $length; $i++) {
		$index = rand(0, $count - 1);
		$result .= mb_substr($chars, $index, 1);
	}

	return $result;
}

function cryptPassword($password){
	return password_hash($password, PASSWORD_DEFAULT);
}

function formatPhone($phone){
	$phone = str_replace('-','',$phone);
	return '('. substr($phone, 0,3) . ') ' . substr($phone,3,3). '-'. substr($phone,6);
}

function formatPostalCode($code){
	$code = strtoupper($code);
	$code = str_replace('-','',$code);
	$code = str_replace(' ','',$code);
	return substr($code,0,3). '-'. substr($code,3,3);
}

?>