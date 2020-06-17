<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-17
 * Time: 11:32
 */
include_once( '../functions.php' );
$module = new Activities();
if(hasPosted(['title', 'units'])){
	$module->add([
		'title' => get('title'),
		'units' => get('units')
	]);
}else{
	echo $module->adminNewForm();
}
?>