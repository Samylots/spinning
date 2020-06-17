<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:12
 */

include_once( '../functions.php' );

if(hasPosted(['type'])){
	$module = new Taxes();
	$tax = $module->getTaxe(get('type'));
	echo  $module->format($tax);
}else{
	fail('Missing arguments');
}
?>