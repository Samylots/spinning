<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-28
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Packages();
$packages = $module->getAll();
$html = "";
while( $package = $packages->fetch() ){
		$html .= '<div id="Package'. $package['id']  .'" class="Package item fix">';
		$html .= '<div id="PackageContent'. $package['id']  .'">';
		$html .=  $module->format($package);
		$html .= '</div>';
		$html .= '</div>';
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucun forfait à afficher...</div>';
}else{
	echo $html;
}
?>