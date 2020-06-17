<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-04
 * Time: 13:20
 */
include_once( '../functions.php' );

$module = new Members();
if(hasPosted(['email', 'password'])){
	$user = new User();

/*	$toto= crypt($password);

	if($toto == crypt($password, $toto))
*/
	$result = $user->login(get('email'),get('password'));
	if(!$result){
		fail();
	}
}else{
	echo $module->loginForm();
}
?>