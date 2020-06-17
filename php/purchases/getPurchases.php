<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
if(hasPosted('memberId')){
	$module = new Purchases(get('memberId'));
}else{
	$module = new Purchases();
}

$purchases = $module->getAll();
$html = "";
while( $purchase = $purchases->fetch() ){
		$html .= '<div id="Purchase'. $purchase['id']  .'" class="item">';
		$html .= '<div id="PurchaseContent'. $purchase['id']  .'">';
		$html .=  $module->format($purchase);
		$html .= '</div>';
		$html .= '</div>';
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucun achat à afficher...</div>';
}else{
	echo $html;
}
?>