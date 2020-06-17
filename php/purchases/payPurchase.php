<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 11:12
 */
include_once( '../functions.php' );
if(hasPosted('purchaseId')){
	$purchases = new Purchases();
	$purchases->markAsPaid(get('purchaseId'));
}else{
	fail();
}