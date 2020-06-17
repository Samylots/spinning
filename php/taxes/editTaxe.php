<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 13:31
 */

include_once( '../functions.php' );
$module = new Taxes();
if(hasPosted(['type', 'percentage'])){
	$result = $module->add([
		'taxe_type_id' => get('type'),
		'percentage_taxe' => get('percentage'),
		'date' => date('Y-m-d H:i:s')
	]);
}else{
	echo $module->adminEditForm($module->getOne(get('id')));
}
?>