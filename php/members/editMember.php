<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 13:31
 */

include_once( '../functions.php' );

$id = get('id');
if($id){
	$module = new Members();
	if(hasPosted(['firstname', 'lastname', 'email', 'phone', 'nickname', 'gender', 'birthdate', 'postalCode'])){
		if($module->checkIfWasACoach($id, get('type'))){
			$result = $module->edit($id, [
				'firstname' => get('firstname'),
				'lastname' => get('lastname'),
				'email' => get('email'),
				'phone' => get('phone'),
				'birthdate' => convertDate(get('birthdate')),
				'gender' => get('gender'),
				'nickname' => get('nickname'),
				'type_id' => get('type'),
				'postal_code' => get('postalCode')
			]);
		}
	}else{
		$member = $module->getOne($id)->fetch();
		echo $module->adminEditForm($member);
	}
}else{
	fail('Missing arguments');
}
?>