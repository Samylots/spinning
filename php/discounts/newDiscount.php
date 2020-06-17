<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-24
 * Time: 17:32
 */
include_once( '../functions.php' );

$module = new Discounts();
if(hasPosted(['code', 'start', 'end', 'type', 'value'])){

	$result = $module->add([
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
	echo $module->adminNewForm();
}
?>