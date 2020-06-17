<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-22
 * Time: 15:41
 */

include_once( '../functions.php' );
$id = get('id');
if($id){
	$module = new Activities();
	$activityObject = $module->createObject($module->getOne($id));
	echo  $module->showActivityTypes($activityObject['id'], $activityObject['types']);

}else{
	fail('Missing arguments');
}

?>