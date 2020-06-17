<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-17
 * Time: 11:32
 */
include_once( '../functions.php' );

$module = new ActivityTypes();
if(hasPosted(['title', 'places', 'color'])){
	$module->add([
		'title' => get('title'),
		'places' => get('places'),
		'color' => get('color'),
		'description' => get('description')
	]);
}else{
	echo $module->adminNewForm();
}
?>