<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new MemberTypes();
$memberTypes = $module->getAll();
$html = "";
while( $memberType = $memberTypes->fetch() ){
	for($i =0; $i < 1; $i++){
		$html .= '<div id="MemberType'. $memberType['id']  .'" class="MemberType item">';
		$html .= '<div id="MemberTypeContent'. $memberType['id']  .'">';
		$html .=  $module->format($memberType);
		$html .= '</div>';
		$html .= '</div>';
	}
}
$module->getToolbars();
if($html == ""){
	echo '<div class="warning">Aucun type de membre à afficher...</div>';
}else{
	echo $html;
}
?>