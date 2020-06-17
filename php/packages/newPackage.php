<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-28
 * Time: 11:32
 */
include_once( '../functions.php' );

$module = new Packages();
if(hasPosted(['start', 'cancel', 'meetings', 'weekly', 'weekAdvance'])){
	$result = $module->add([
		'registration_deadline' => getTime(get('start')),
		'cancellation_deadline' => getTime(get('cancel')),
		'meetings_allowed' => get('meetings'),
		'weekly' => get('weekly'),
		'limit_registration_advance' => get('weekAdvance')
	]);
}else{
	echo $module->adminNewForm();
}
?>