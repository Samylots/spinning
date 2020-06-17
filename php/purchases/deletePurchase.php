<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 11:12
 */
include_once( '../functions.php' );
$user = new User();
if(hasPosted('purchaseId') && $user->isAdmin()){
	$purchases = new Purchases();
	$purchases->deepDelete(get('purchaseId'));
	//TODO enlever les registrations etc....?
}else{
	fail();
}