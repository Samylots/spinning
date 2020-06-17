<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 17:15
 */
include_once( '../functions.php' );
if(hasPosted(['memberId'])){
	$members = new Members();
	echo $members->getFutureMeetingsCoaching(get('memberId'));
}else{
	fail();
}
?>