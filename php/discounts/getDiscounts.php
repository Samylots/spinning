<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Discounts();
$discounts = $module->createObject($module->getAll());
$html = "";
foreach($discounts as $discount){
		$html .= '<div id="Discount'. $discount['id']  .'" class="ModuleItem item fix">';
		$html .= '<div id="DiscountContent'. $discount['id']  .'">';
		$html .=  $module->format($discount);
		$html .= '</div>';
		$html .= '</div>';
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucun rabais à afficher...</div>';
}else{
	echo $html;
}
?>