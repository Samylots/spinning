<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Companies();
$companies = $module->getAll();
$html = "";
while( $companie = $companies->fetch() ){
		$html .= '<div id="Companie'. $companie['id']  .'" class="Companie item fix">';
		$html .= '<div id="CompanieContent'. $companie['id']  .'">';
		$html .=  $module->format($companie);
		$html .= '</div>';
		$html .= '</div>';
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucune compagnie à afficher...</div>';
}else{
	echo $html;
}
?>