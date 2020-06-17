<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Activities();
$activities = $module->getAll();
$actualId = null;
$html = "";
while( $activity = $activities->fetch() ){
	if($actualId != $activity['id']){
		$actualId = $activity['id'];
		$html .= '<div id="ModuleItem'. $activity['id']  .'" class="ModuleItem item fix">';
		$html .= '<div id="ActivityContent'. $activity['id']  .'">';
		$html .=  $module->format($module->getOne($actualId));
		$html .= '</div>';
		$html .= '</div>';
	}
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucune activité à afficher...</div>';
}else{
	echo $html;
}
?>