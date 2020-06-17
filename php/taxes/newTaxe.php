<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-28
 * Time: 00:00
 */

include_once( '../functions.php' );
if(hasPosted('type')){
	$module = new Taxes();
	if (hasPosted('percentage')){
		$result = $module->add([
			'taxe_type_id' => get('type'),
			'percentage_taxe' => get('percentage'),
			'date' => date('Y-m-d H:i:s')
		]);
	} else{
		echo $module->adminNewForm();
	}
}else{
	fail();
}
?>