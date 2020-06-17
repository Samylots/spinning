<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-22
 * Time: 14:24
 */

include_once( '../functions.php' );
	$module = new Activities();
	$availableActivities = $module->getAll();
	$activitiesObject = $module->createObject($availableActivities);
	$html = "<select id='ActivitySelector'>";
	$options = "";
	while($type = $availableTypes->fetch()){
		$options .= '<option value="'. $activitiesObject['id'] .'">'. $activitiesObject['title'] . '</option>';
	}
	$html .= $options . "</select>";
	echo $html;
?>



