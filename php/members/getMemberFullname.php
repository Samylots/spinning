<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:12
 */

include_once( '../functions.php' );

if(hasPosted('memberId')){
	$module = new Members();
	$member = $module->getOne(get('memberId'))->fetch();
	echo  $module->getFullName($member);
}else{
	fail('Missing arguments');
}
?>