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
	$schedule = $weekModule->format($weekModule->getAll());
	if($schedule != ""){
		$html =  '<div id="schedule" class="item">';
		$html .= '<div class="scheduleTitle">'. $sessionModule->getWeekTitle(get('sessionId'),$weekId) ;
		$html .= '<span id="actions" class="actions">';
		$html .= '<button class="button edit" onclick='. "\"openWeek(" . ($weekId-1). ")\"". ($weekId <= 1 ? 'disabled' : '') .'>Semaine précédente</button>';
		$html .= '<button class="button edit" onclick='. "\"openWeek(" . ($weekId+1). ")\"". ($weekId >= $session['total_weeks'] ? 'disabled' : '') .'>Semaine suivante</button>';
		$html .= '</span>';
		$html .= '</div>';
		$html .= '<div class="scheduleTable">';
		$html .= $schedule;
		$html .= '</div>';
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