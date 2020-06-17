<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 17:15
 */
include_once( '../functions.php' );
if(hasPosted(['meetingId'])){
	$user = new User();
	$coaches = new Coaches(get('meetingId'));
	echo $coaches->listActualCoaches($user->isAdmin());
}else{
	fail();
}
?>