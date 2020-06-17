<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-23
 * Time: 10:36
 */
include_once( '../functions.php' );
if(hasPosted('id')){
	$module = new Schedules(get('id'));

	if ( hasPosted([ 'start', 'end', 'day', 'activity','subscriptionPlaces' ]) ){
		if(!$module->add([
			'start' => getTime(get('start')),
			'end' => getTime(get('end')),
			'day_id' => get('day'),
			'session_id' => get('id'),
			'activity_id' => get('activity'),
			'subscription_places' => get('subscriptionPlaces')
		])){
			fail('ERROR WHILE ADDING');
		};
	} else{
		echo $module->adminNewForm();
	}
}else{
	redirect('administration.php');
}
?>