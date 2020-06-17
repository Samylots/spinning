<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 17:03
 */
include_once( '../functions.php' );
if(hasPosted(['meetingId'])){
	$coaches = new Coaches(get('meetingId'));
	echo $coaches->listAvailableCoaches();
}else{
	fail();
}
?>