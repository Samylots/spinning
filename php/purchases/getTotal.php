<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 11:03
 */
include_once( '../functions.php' );
if(hasPosted(['sessionId','packagesType','code'])){
	if(empty(get('packagesType'))){
		alert('Vous devez choisir le forfait!');
	}
	$purchases = new Purchases();
	$taxes = new Taxes();
	if(hasPosted('selectedPeriods')){
		$price = $purchases->getSubscriptionSubtotal(get('sessionId'),get('packagesType'),json_decode(get('selectedPeriods')));
	}else{
		$price = $purchases->getCardSubtotal(get('sessionId'),get('packagesType'));
	}
	$price = $purchases->processDiscount($price, get('code'));
	$taxes = $taxes->getTPS($price) + $taxes->getTVQ($price);
	echo formatPrice($price+$taxes,2);
}else{
	fail();
}
?>