<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Sessions();
$sessions = $module->getAll();
$html = "";
while( $session = $sessions->fetch() ){
	for($i =0; $i < 1; $i++){
		$html .= '<div id="Session'. $session['id']  .'" class="Session item fix">';
		$html .= '<div id="SessionContent'. $session['id']  .'">';
		$html .=  $module->format($session);
		$html .= '</div>';
		$html .= '</div>';
	}
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucune session à afficher...</div>';
}else{
	echo $html;
}
?>