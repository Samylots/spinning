<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 17:15
 */
include_once( '../functions.php' );
if(hasPosted('meetingId')){
	$meetings = new Meetings();
	$registrations = new Registrations();
	$Actualregistrations = $registrations->getMeetingRegistrations(get('meetingId'));
	$html ='';
	if(!$meetings->isActive(get('meetingId'))){
		$html .= '<span class="expired">Scéance annulé!</span>';
	}else{
		$registrations->showPlacesLeft(get('meetingId'));
		$nbOfSubscription =1;
		while( $registration = $Actualregistrations->fetch() ){
			$html .= '<div class="name">'. $nbOfSubscription . ': '. $registrations->formatName($registration) . ' </div>';
			$nbOfSubscription++;
		}
	}
	echo $html;
}else{
	fail();
}
?>