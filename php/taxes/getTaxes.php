<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-27
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Taxes();
$taxes = $module->getAll();
$html = "";
while( $tax = $taxes->fetch() ){
	for($i =0; $i < 1; $i++){
		$html .= '<div id="Taxe'. $tax['id']  .'" class="item">';
		$html .= '<div id="TaxeContent'. $tax['id']  .'">';
		$html .=  $module->format($tax);
		$html .= '</div>';
		$html .= '</div>';
	}
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucune taxe à afficher...</div>';
}else{
	echo $html;
}
?>