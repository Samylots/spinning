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
if(hasPosted('meetingId')){
	if ( $registrations->isMeetingFull(get('meetingId')) ){
		kill();
	}
}else{
	fail();
}