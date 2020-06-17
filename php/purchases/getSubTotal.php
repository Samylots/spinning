<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 10:59
 */
include_once( '../functions.php' );
if(hasPosted(['sessionId','packagesType'])){
	$subtotal = 0;
	$purchases = new Purchases();
	if(hasPosted('selectedPeriods')){
		$subtotal = $purchases->getSubscriptionSubtotal(get('sessionId'),get('packagesType'),json_decode(get('selectedPeriods')));
	}else{
		$subtotal = $purchases->getCardSubtotal(get('sessionId'),get('packagesType'));
	}

	echo formatPrice($subtotal,2) ;
}else{
	fail();
}
?>