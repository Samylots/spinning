<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
if(hasPosted('id')){
	$module = new Rates(get('id'));
	$sessions = $module->getAll();
	$html = "";
	while( $rate = $sessions->fetch() ){
		for($i =0; $i < 1; $i++){
			$html .= '<div id="Rate'. $rate['subscription_type_id']  .'" class="item">';
			$html .= '<div id="RateContent'. $rate['subscription_type_id']  .'">';
			$html .=  $module->format($rate);
			$html .= '</div>';
			$html .= '</div>';
		}
	}
	$module->getToolbars();
	if($html == ""){
		echo '<div class="warning">Aucun tarif à afficher...</div>';
	}else{
		echo $html;
	}
}else{
	redirect('../../administration.php');
}
?>