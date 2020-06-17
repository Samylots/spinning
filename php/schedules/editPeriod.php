<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-23
 * Time: 13:02
 */
include_once( '../functions.php' );
if(hasPosted(['id','periodId'])){
	$module = new Schedules(get('id'));

	if ( hasPosted([ 'start', 'end', 'day', 'activity','subscriptionPlaces' ]) ){
		$module->edit(get('periodId'),[
			'start' => getTime(get('start')),
			'end' => getTime(get('end')),
			'day_id' => get('day'),
			'session_id' => get('id'),
			'activity_id' => get('activity'),
			'subscription_places' => get('subscriptionPlaces')
		]);
	} else{
		echo $module->adminEditForm($module->getOne(get('periodId'))->fetch());
	}
}else{
	redirect('administration.php');
}
?>