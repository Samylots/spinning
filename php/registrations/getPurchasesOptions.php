<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 14:21
 */
include_once( '../functions.php' );
$user = new User();
$registrations = new Registrations();
if(hasPosted(['memberId','meetingId'])){
	echo $registrations->createPurchasesOptions(get('meetingId'),get('memberId'));
}else if($user->isLogged()){
	echo $registrations->createPurchasesOptions(get('meetingId'),$user->getUserId());
}else{
	fail();
}