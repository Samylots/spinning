<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:12
 */

include_once( '../functions.php' );

if(hasPosted('id')){
	if ( hasPosted('id', 'package') ){
		$module = new Rates(get('id'));
		$session = $module->getOne(get('package'))->fetch();
		echo $module->format($session);
	} else{
		fail('Missing arguments');
	}
}else{
	redirect('administration.php');
}
?>