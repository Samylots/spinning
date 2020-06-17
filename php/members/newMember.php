<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-17
 * Time: 11:32
 */
include_once( '../functions.php' );

$module = new Members();
if(hasPosted(['firstname', 'lastname', 'email', 'phone', 'nickname', 'gender', 'birthdate', 'postalCode'])){
	if(!$user){
		$generatedPassword = generateRandomSequence();
		$result = $module->add([
			'firstname' => get('firstname'),
			'lastname' => get('lastname'),
			'email' => get('email'),
			'phone' => get('phone'),
			'birthdate' => convertDate(get('birthdate')),
			'gender' => get('gender'),
			'nickname' => get('nickname'),
			'password' => cryptPassword($generatedPassword),
			'type_id' => get('type'),
			'postal_code' => get('postalCode'),
			'created'=> now()
		]);
		//@TODO send generated password by email... otherwise it will be lost!
		//email $generatedPassword
	}else{
		fail('ALREADY EXIST');
	}
}else{
	echo $module->adminNewForm();
}
?>