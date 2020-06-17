<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-22
 * Time: 20:52
 */
include_once( '../functions.php' );

if(hasPosted('id')){
	$scheduleModule = new Schedules(get('id'));
	$html = "";
	$schedule = $scheduleModule->format($scheduleModule->getAll());
	$sessionModule = new Sessions();
	$session = $sessionModule->getOne(get('id'))->fetch();
	if($schedule != ""){
		$html =  '<div id="schedule" class="item">';
		$html .= '<div class="scheduleTitle">Horaire "'. $session['title'] .'" du '. formatDateTime($session['start_date']) .' au '. formatDateTime($session['end_date']) .'</div>';
		$html .= '<div class="scheduleTable">';
		$html .= $schedule;
		$html .= '</div>';
		$html .= '</div>';
	}
	$scheduleModule->getToolbars();
	if ( $html == "" ){
		echo '<div class="warning">Aucun horaire à afficher...</div>';
	} else{
		echo $html;
	}
}else{
	redirect('administration.php');
}
?>