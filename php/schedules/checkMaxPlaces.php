<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 14:21
 */
include_once( '../functions.php' );
$user = new User();
$activities = new Activities();
if(hasPosted(['activityId','places'])){
	$maxPlaces = $activities->getMinPlaces(get('activityId'));
	if ( $activities->getMinPlaces(get('activityId')) < get('places') ){
		echo json_encode('Le nombre de places maximum est de ' . plurialNoun($maxPlaces, 'place'));
	}else{
		echo "true";
	}
}else{
	fail();
}