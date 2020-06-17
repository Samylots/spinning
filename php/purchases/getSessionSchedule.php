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
	if(get('packageId')){
		echo $purchases->getSessionSchedule(get('sessionId'));
	}else{
		echo 'Veuillez choisir votre forfait pour l\'abonnement!';
	}
}else{
	fail();
}