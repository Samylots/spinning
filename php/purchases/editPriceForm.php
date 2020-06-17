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
	if(hasPosted(['purchaseId','price'])){
		$module->editPrice(get('purchaseId'),get('price'));
	}else{
		echo $module->editPriceForm(get('purchaseId'));
	}
}else{
	fail();
}
?>