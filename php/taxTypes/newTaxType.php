<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-17
 * Time: 11:32
 */
include_once( '../functions.php' );

$module = new TaxTypes();
if(hasPosted('code')){
	$result = $module->add([
		'title' => get('code')
	]);
	if(!$result){
		fail();
	}
	echo $module->getLast();
}else{
	echo $module->adminNewForm();
}
?>