<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-22
 * Time: 15:04
 */
include_once( '../functions.php' );
if(hasPosted(['id','idType'])){
	DB::getInstance()->add('activities_activity_types', [
		'activity_id' => get('id'),
		'activity_type_id' => get('idType'),
		'time' => now()
	]);
}else{
	fail('Missing args');
}
?>