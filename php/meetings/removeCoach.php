<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 16:03
 */
include_once( '../functions.php' );
if(hasPosted(['meetingId','coachId'])){
	$coaches = new Coaches(get('meetingId'));
	$coaches->removeCoach(get('coachId'));
}else{
	fail('NOPE');
}
?>