<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-05
 * Time: 09:43
 */
include_once( '../functions.php' );
if(isPostRequest()){
$module = new Members();
	$user = new User();
	$user->logout();
}else{
	fail();
}
?>