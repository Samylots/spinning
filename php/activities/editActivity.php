<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 13:31
 */

include_once( '../functions.php' );

$id = get('id');
if($id){

	$module = new Activities();
	if(hasPosted(['title', 'units'])){
		$unitsTest = floatval(get('units'));
		if(gettype($unitsTest) != "integer" && gettype($unitsTest) != "double"){
			invalid();
		}
		$result = $module->edit($id, [
				'title' => get('title'),
				'units' => get('units')
			]);
	}else{
		$activity = $module->getOne($id);
		echo $module->adminEditForm($activity);
	}
}else{
	fail('Missing arguments');
}
?>