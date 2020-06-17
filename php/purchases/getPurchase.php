<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Purchases();
if(hasPosted('purchaseId')){
	$purchase = $module->getOne(get('purchaseId'),false,true)->fetch();
	echo $module->format($purchase);
}else{
	fail();
}
?>