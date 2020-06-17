<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 13:31
 */

include_once( '../functions.php' );
$user = new User();
$id = get('id');
if($id){
	$module = new Members();
	$canChangePasswordWithoutOldPassword = false;
	if(!$module->isAdmin($id)){
		$canChangePasswordWithoutOldPassword = true;
	}
	if($user->isAdmin() && $canChangePasswordWithoutOldPassword){
		if(hasPosted('newPassword')){
			$result = $module->edit($id, [
				'password' => cryptPassword(get('newPassword'))
			]);
		}else{
			echo $module->adminEditPasswordForm();
		}
	}else{
		if(hasPosted(['oldPassword', 'newPassword'])){
			if($module->checkIfWasOldPassword($id, get('oldPassword'))){
				$result = $module->edit($id, [
					'password' => cryptPassword(get('newPassword'))
				]);
			}
		}else{
			echo $module->editPasswordForm();
		}
	}
}else{
	fail('Missing arguments');
}
?>