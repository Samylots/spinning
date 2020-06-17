<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-17
 * Time: 11:32
 */
include_once( '../functions.php' );

$module = new Companies();
if(hasPosted(['title'])){
	$result = $module->add([
		'title' => get('title')
	]);
}else{
	echo $module->adminNewForm();
}
?>