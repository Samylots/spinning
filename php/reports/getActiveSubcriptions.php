<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
$module = new Reports();
$rows = $module->getActiveSubscriptions();
$html = $module->startTable();
while( $row = $rows->fetch() ){
		$html .= $module->formatActiveRow($row);
}
$html .= $module->endTable();

$module->getToolbars();
	echo $html;
?>