<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-22
 * Time: 15:04
 */

include_once( '../functions.php' );
if(hasPosted(['id','idType'])){
	$queryString = "DELETE FROM activities_activity_types
					where activity_id = ". get('id') ."
					AND activity_type_id =". get('idType');

	$result = DB::getInstance()->customQuery($queryString);
	echo $result;
}else{
	fail('Missing args');
}
?>