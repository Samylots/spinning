<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-17
 * Time: 11:32
 */
include_once( '../functions.php' );

$module = new Sessions();
if(hasPosted(['title', 'start', 'end','placesDate'])){
	$result = $module->add([
		'title' => get('title'),
		'start_date' => get('start'),
		'end_date' => get('end'),
		'subscription_places_date' =>get('placesDate'),
		'total_weeks' => $module->getNbOfWeeks(get('start'),get('end'))
	]);
}else{
	echo $module->adminNewForm();
}
?>