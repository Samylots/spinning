<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new ActivityTypes();
$types = $module->getAll();
$html = "";
while( $type = $types->fetch() ){
	for($i =0; $i < 1; $i++){
		$html .= '<div id="ActivityType'. $type['id']  .'" class="ActivityType item fix">';
		$html .= '<div id="ActivityTypeContent'. $type['id']  .'">';
		$html .=  $module->format($type);
		$html .= '</div>';
		$html .= '</div>';
	}
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucun type d\'activité à afficher...</div>';
}else{
	echo $html;
}
?>