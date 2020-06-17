<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-28
 * Time: 13:31
 */

include_once( '../functions.php' );

$id = get('id');
if($id){
	$module = new Packages();
	if(hasPosted(['start', 'cancel', 'meetings', 'weekly', 'weekAdvance'])){
		$result = $module->edit($id, [
				'registration_deadline' => getTime(get('start')),
				'cancellation_deadline' => getTime(get('cancel')),
				'meetings_allowed' => get('meetings'),
				'weekly' => get('weekly'),
				'limit_registration_advance' => get('weekAdvance')
			]);
	}else{
		$session = $module->getOne($id)->fetch();
		echo $module->adminEditForm($session);
	}
}else{
	fail('Missing arguments');
}
?>