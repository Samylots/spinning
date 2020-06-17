<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:12
 */

include_once( '../functions.php' );

$id = get('id');
if($id){
	$module = new Discounts();
	$session = $module->createObject($module->getOne($id));
	echo  $module->format($session[0]);

}else{
	fail('Missing arguments');
}
?>