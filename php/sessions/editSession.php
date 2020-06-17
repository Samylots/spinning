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
	$module = new Sessions();
	if(hasPosted(['title', 'start', 'end','placesDate'])){
		$result = $module->edit($id, [
				'title' => get('title'),
				'start_date' => get('start'),
				'end_date' => get('end'),
				'subscription_places_date' => get('placesDate'),
				'total_weeks' => $module->getNbOfWeeks(get('start'),get('end'))
			]);
	}else{
		$session = $module->getOne($id)->fetch();
		$currentDate = date('Y-m-d');
		$startDate = date('Y-m-d', strtotime($session['start_date']));
		$endDate = date('Y-m-d', strtotime($session['end_date']));
		//if($currentDate < $startDate){
			echo $module->adminEditForm($session);
		/*}else{
			if($currentDate < $endDate){
				fail("en cours");
			}else{
				fail("terminé");
			}
		}*/
	}
}else{
	fail('Missing arguments');
}
?>