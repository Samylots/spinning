<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-23
 * Time: 13:29
 */
include_once( '../functions.php' );

if(hasPosted(['periodId', 'id'])){
	$module = new Schedules(get('id'));
	$module->delete(get('periodId'));
}else{
	fail();
}
?>