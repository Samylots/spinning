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
	$module = new Members();
	$member = $module->getOne($id)->fetch();
	echo  $module->memberFormat($member);
}else{
	fail('Missing arguments');
}
?>