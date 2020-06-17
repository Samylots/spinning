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
	$module = new Sessions();
	$session = $module->getOne($id)->fetch();
	echo  $module->format($session);

}else{
	fail('Missing arguments');
}
?>