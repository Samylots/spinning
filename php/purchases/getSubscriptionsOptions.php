<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 14:21
 */
include_once( '../functions.php' );
if(hasPosted(['sessionId'])){
	$purchases = new Purchases();
	echo $purchases->createPackageOptions($purchases->getSubscriptions(get('sessionId')));
}else{
	fail();
}