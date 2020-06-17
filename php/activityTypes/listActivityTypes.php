<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-22
 * Time: 14:24
 */

include_once( '../functions.php' );
if(hasPosted(['id'])){
	$id = get('id');
	$queryString = "SELECT type.active, type.id, type.title, type.places, type.color, type.active
					FROM activity_types as type
					WHERE type.id NOT IN (
					    SELECT activity_type_id
					    FROM activities_activity_types
					    where activity_id =". $id ." ) AND active = true";

	$availableTypes = DB::getInstance()->customQuery($queryString);

	$html = "<select id='typeSelector'>";
	$options = "";
	while($type = $availableTypes->fetch()){
		$options .= '<option value="'. $type['id'] .'">'. $type['title'] . "(". plurialNoun($type['places'], 'place') .')</option>';
	}
	$html .= $options . "</select>";
	if(!empty($options)){
		echo $html;
	}else{
		fail('No more type available');
	}
}else{
	fail('Missing arguments');
}
?>



