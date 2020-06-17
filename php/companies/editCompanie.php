<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 13:31
 */

include_once( '../functions.php' );

$id = get('id');
if($id){

	$module = new Companies();
	if(hasPosted(['title'])){
		$result = $module->edit($id, [
				'title' => get('title')
			]);
	}else{
		$companie = $module->getOne($id)->fetch();
		echo $module->adminEditForm($companie);
	}
}else{
	fail('Missing arguments');
}
?>