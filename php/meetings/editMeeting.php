<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-23
 * Time: 13:02
 */
include_once( '../functions.php' );
if(hasPosted(['sessionId', 'meetingId'])){
	$registrations = new Registrations();
	$weekModule = new WeekSchedules(get('sessionId'));

	if ( hasPosted([ 'start', 'end', 'active']) ){
		$weekModule->edit(get('meetingId'),[
			'start' => getTime(get('start')),
			'end' => getTime(get('end')),
			'active' => get('active')
		],true);
		$registrations->updateRegistrationsOnMeetingStatus(get('meetingId'), get('active'));
	} else{
		echo $weekModule->adminEditForm($weekModule->getOne(get('meetingId'))->fetch());
	}
}else{
	redirect('administration.php');
}
?>