<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-28
 * Time: 14:15
 */
include_once( '../functions.php' );

$id = get('id');
if($id){
	$module = new Packages();
	$module->delete($id);
}else{
	fail();
}
?>