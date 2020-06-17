<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-13
 * Time: 13:02
 */
include_once( '../functions.php' );
if(hasPosted('meetingId')){
	$meetings = new Meetings();
	$registrations = new Registrations();
	if ( hasPosted([ 'receiptId', 'name']) ){
		$registrations->registerMember(get('meetingId'), get('receiptId'), get('name'));
	}else{
		echo $registrations->format($meetings->getOne(get('meetingId'))->fetch());
	}
}else{
	redirect('index.php');
}
?>