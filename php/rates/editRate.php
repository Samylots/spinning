<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-23
 * Time: 13:02
 */
include_once( '../functions.php' );
if(hasPosted(['id','package'])){
	$module = new Rates(get('id'));
	if ( hasPosted('price') ){
		$module->edit(get('package'),[
			'rate' => get('price')
		]);
	} else{
		echo $module->adminEditForm($module->getOne(get('package'))->fetch());
	}
}else{
	redirect('administration.php');
}
?>