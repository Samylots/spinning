<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-23
 * Time: 13:31
 */

include_once( '../functions.php' );

$id = get('id');
if($id){

	$module = new Discounts();
	if(hasPosted(['code', 'start', 'end', 'type', 'value'])){
		$result = $module->edit($id, [
			'alias' => get('code'),
			'start' => get('start'),
			'expiration' => get('end'),
			'minimumAge' => get('minAge'),
			'description' => get('description'),
			'type' => get('type'),
			'value' => get('value'),
			'company_id' => get('company')
			]);
	}else{
		$session = $module->createObject($module->getOne($id));
		echo $module->adminEditForm($session[0]);
	}
}else{
	fail('Missing arguments');
}
?>