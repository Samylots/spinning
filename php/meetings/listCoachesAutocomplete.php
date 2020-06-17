<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 17:03
 */
include_once( '../functions.php' );
if(hasPosted(['meetingId','q'])){
	$coaches = new Coaches(get('meetingId'));
	$availableCoaches = $coaches->getAllAutocomplete(get('q'));
	$actualCoaches = [];
	while($coach = $availableCoaches->fetch()){
		$actualCoaches[] = $coach;
	}
	echo json_encode($actualCoaches);
}else{
	fail();
}
?>