<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-23
 * Time: 10:36
 */
include_once( '../functions.php' );
if(hasPosted('id')){
	$module = new Rates(get('id'));
	if ( hasPosted([ 'package', 'price' ]) ){
		$module->add([
			'subscription_type_id' => get('package'),
			'session_id' => get('id'),
			'rate' => get('price')
		]);
	} else{
		echo $module->adminNewForm();
	}
}else{
	redirect('../../administration.php');
}
?>