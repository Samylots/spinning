<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-04
 * Time: 12:58
 */
include_once( '../functions.php' );

$module = new Members();
if(hasPosted(['firstname', 'lastname', 'email', 'phone', 'nickname', 'gender', 'birthdate', 'password', 'postalCode'])){
	$user = new User();
	$result = $module->add([
		'firstname' => get('firstname'),
		'lastname' => get('lastname'),
		'email' => get('email'),
		'phone' => get('phone'),
		'birthdate' => convertDate(get('birthdate')),
		'gender' => get('gender'),
		'nickname' => get('nickname'),
		'password' => cryptPassword(get('password')),
		'type_id' => 1,
		'postal_code' => get('postalCode'),
		'created'=> now()
	]);
	$result = $user->login(get('email'),get('password'));
	if(!$result){
		fail('CANT LOGIN');
	}
}else{
	echo $module->registrationForm();
}
?>