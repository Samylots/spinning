<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 09:30
 */

include_once ('../functions.php');
if(hasPosted('purchaseId')){
	$module = new Purchases();
	if(hasPosted(['purchaseId','refund'])){
		$module->refundPurchase(get('purchaseId'),get('refund'));
	}else{
		echo $module->refundPurchaseForm(get('purchaseId'));
	}
}else{
	fail();
}
?>