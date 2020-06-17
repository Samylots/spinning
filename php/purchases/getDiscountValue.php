<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 10:55
 */
include_once( '../functions.php' );
if(hasPosted(['sessionId','packagesType','code'])){
	$rates = new Rates(get('sessionId'));
	if(empty(get('packagesType'))){
		alert('Vous devez choisir le forfait avant de pouvoir appliquer un rabais!');
	}
	$rate = $rates->getOne(get('packagesType'))->fetch();
	$discounts = new Discounts();
	$value = $discounts->getDiscountValue(get('code'),$rate['rate']);
	echo formatPrice($value,2);
}else{
	fail();
}
?>