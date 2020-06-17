<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 16:02
 */
include_once( '../functions.php' );
if(hasPosted(['meetingId','coachId'])){
	$coaches = new Coaches(get('meetingId'));
	$coaches->addCoach(get('coachId'));
}else{
	fail();
}
?>