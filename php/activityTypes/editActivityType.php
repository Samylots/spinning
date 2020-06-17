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
	$module = new ActivityTypes();
	if(hasPosted(['title', 'places', 'color'])){
		$result = $module->edit($id, [
				'title' => get('title'),
				'places' => get('places'),
				'color' => get('color'),
				'description' => get('description')
			]);
	}else{
		$tax = $module->getOne($id)->fetch();
		echo $module->adminEditForm($tax);
	}
}else{
	fail('Missing arguments');
}
?>