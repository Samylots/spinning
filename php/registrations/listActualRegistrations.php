<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 17:15
 */
include_once( '../functions.php' );
if(hasPosted('meetingId')){
	$registrations = new Registrations();
	if(hasPosted('memberId')){
		$memberId = get('memberId');
	}else{
		$user = new User();
		$memberId = $user->getUserId();
	}
	echo $registrations->listActualRegistrations(get('meetingId'),$memberId);
}else{
	fail();
}
?>