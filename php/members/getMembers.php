<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Members();
$members = $module->getAll();
$html = "";
while( $member = $members->fetch() ){
		$html .= '<div id="Member'. $member['id']  .'" class="Member item fix">';
		$html .= '<div id="MemberContent'. $member['id']  .'">';
		$html .=  $module->format($member);
		$html .= '</div>';
		$html .= '</div>';
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucun membre à afficher...</div>';
}else{
	echo $html;
}
?>