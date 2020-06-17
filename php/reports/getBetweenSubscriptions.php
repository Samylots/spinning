<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
if(hasPosted(['start','end'])){
	$module = new Reports();
	$rows = $module->getSubscriptionsBetween(get('start'),get('end'));
	$html = $module->startTable();
	while( $row = $rows->fetch() ){
		$html .= $module->formatRow($row);
	}
	$html .= $module->endTable();

	$module->getToolbars();
	echo $html;
}else{
	fail();
}
?>