<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-05
 * Time: 11:56
 */
include_once( '../functions.php' );

if(hasPosted(['sessionId', 'weekId'])){
	$sessionModule = new Sessions();
	$session = $sessionModule->getOne(get('sessionId'))->fetch();
	$weekId = $sessionModule->getCorrectedWeek(get('sessionId'),get('weekId'));

	$weekModule = new WeekSchedules(get('sessionId'), $weekId);
	$html = "";
	$schedule = $weekModule->memberFormat($weekModule->getAll());
	if($schedule != ""){
		$html =  '<div id="schedule" class="item">';
		$html .= '<div class="scheduleTitle">'. $sessionModule->getWeekTitle(get('sessionId'),$weekId);
		$html .= '<span id="actions" class="actions">';
		$html .= '<button class="button edit" onclick='. "\"openPublicWeek(" . ($weekId-1). ")\"". ($weekId <= 1 ? 'disabled' : '') .'>Semaine précédente</button>';
		$html .= '<button class="button edit" onclick='. "\"openPublicWeek(" . ($weekId+1). ")\"". ($weekId >= $session['total_weeks'] ? 'disabled' : '') .'>Semaine suivante</button>';
		$html .= '</span>';
		$html .= '</div>';
		$html .= '<div class="scheduleTable">';
		$html .= $schedule;
		$html .= '</div>';
		$html .= '<div class="item">Cet horaire peut être sujet à changement: compte tenu d\'un imprévu ou de ma ladie d\'un entraîneur. Vos commentaires et suggestions sont toujours appréciés, Merci!</div>';
		$html .= '</div>';
	}
	$weekModule->getToolbars();
	if ( $html == "" ){
		fail('AN ERROR OCCURED WHILE SHOWING WEEK SESSION...');
	} else{
		echo $html;
	}
}else{
	redirect('administration.php');
}
?>