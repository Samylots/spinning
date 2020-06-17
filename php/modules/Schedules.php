<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Schedules extends Modules implements MemberFormatter
{
	private $actualDay = 0;
	private $imgPath = "img/schedules/";
	private $actualId;
	/**
	 * Schedules constructor.
	 * @param string $table
	 */
	function __construct( $sessionId) {
		$this->actualId = $sessionId;
		parent::__construct('periods');
		$this->adminToolbar->addOption('Nouvelle période',null,'newPeriod()');
	}

	public function getAll(){
		return DB::getInstance()->customQuery('SELECT sessions.id as sessionId, p.id as periodId, d.id
			as dayId, d.title as dayTitle, p.start, p.end, ac.id as activityId, units, places, subscription_places
			 FROM sessions
			 LEFT JOIN '. $this->table . ' as p
			 on sessions.id = p.session_id
			 LEFT JOIN activities as ac
			 on p.activity_id = ac.id
			 LEFT JOIN (
				SELECT activity_id, type.title, min(places) as places
			    FROM activities_activity_types as actypes
				LEFT JOIN activity_types as type
				on actypes.activity_type_id = type.id
			    group by actypes.activity_id
			    order by places asc) as types
			 on p.activity_id = types.activity_id
			 LEFT JOIN days as d
			 on d.id = p.day_id
			 WHERE sessions.id = '. $this->actualId . ' AND p.active = true order by dayId');
	}

	public function add( $data ){
		try {
			$result = DB::getInstance()->add($this->table, $data);
			$meetingData = [
				'start' =>$data['start'],
				'end' =>$data['end'],
				'period_id' =>DB::getInstance()->getLast(),
				'week' => 1
			];
			$sessionModule = new Sessions();
			$session = $sessionModule->getOne($this->actualId)->fetch();
			$sessionWeekModule = new WeekSchedules($this->actualId);
			for($i =1; $i <= $session['total_weeks']; $i++){
				$sessionWeekModule->setWeek($i);
				$result &= $sessionWeekModule->add($meetingData);
				$meetingData['week']++;
			}
			return $result;
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function edit( $id, $data, $debug = false){
		try { //custom update string because of period_id= clause...
			$queryString = 'UPDATE meetings';
			$sets = [];
			$meetingData = [
				'start' =>$data['start'],
				'end' =>$data['end']
			];
			foreach( $meetingData as $key => $value ){
				if(DB::getInstance()->getValue($value) != false){
					$sets[] = $key . '=' . DB::getInstance()->getValue($value);
				}else{
					$sets[] = $key . '=' . 'null';
				}
			}
			$queryString .= ' SET '. implode(', ', $sets);
			$queryString .= ' WHERE ' . 'period_id='. $id;
			DB::getInstance()->customQuery($queryString); //Don't use EXEC because if you edit no meetings, it will "fails"!
			return DB::getInstance()->update($id, $this->table, $data);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function delete( $id ){
		try {
			$result = true;
			$queryString = 'UPDATE meetings SET active=false WHERE period_id=' . $id;
			$result &= DB::getInstance()->exec($queryString);
			if($result){
				$registrations = new Registrations();
				$registrations->deletePeriodRegistrations($id);
			}
			return DB::getInstance()->delete($id, $this->table) & $result;
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function format( $schedule){
		parent::testRequest();
		$scheduleObject = $this->createObject($schedule);
		$html = "";
		foreach($scheduleObject as $day){
			$html .= $this->formatDay($day);
		}
		$html .= $this->createEmptyDayUntil(7);
		return $html;
	}

	public function adminEditForm( $period){
		parent::testRequest();
		$sessions = new Sessions();
		$activities = new Activities();
		$session = $sessions->getOne($this->actualId)->fetch();
		return '<form id="form'. $period['id'] .'">
			<div id="scheduleInfo'. $period['id'] .'" class="inputs">'.
				Helper::tip('Session: ' .$sessions->formatTitle($session)). '<br>'.
				Helper::listWeekDays() . $activities->listActivities().
				'Nombre de places réservées pour les abonnements:
				<input id="newPeriodsubscriptionPlaces" name="newPeriodsubscriptionPlaces" type="number" placeholder="Nombre de places réservées pour les abonnements" value="'. $period['subscription_places'] .'" required>
				Heure de début:
				<input id="NewPeriodStart" name="NewPeriodStart" class="hasWickedpicker" type="text" placeholder="Heure de début" required readonly value="'. $period['start'] .'">
				Heure de fin:
				<input id="NewPeriodEnd" name="NewPeriodEnd" class="hasWickedpicker" type="text" placeholder="Heure de début" required readonly value="'. $period['end'] .'">
				<button class="button cancel" onclick=' . "'deletePeriod(\"" . $period['id'] . "\")'" . '>Supprimer la période</button>
			</div>'.
		'</form>
			<script>
             initTime("#NewPeriodStart","'. $period['start'] .'");
            initTime("#NewPeriodEnd","'. $period['end'] .'");
			$("#DaySelector").val("'. $period['day_id'] .'");
			$("#ActivitySelector").val("'. $period['activity_id'] .'");
			$("#form'. $period['id'] .'").validate({
				rules:{
					newPeriodsubscriptionPlaces:{
						required: true,
						remote: {
					        url: "php/schedules/checkMaxPlaces.php",
					        type: "post",
					        data: {
					          activityId: function() {
					            return $( "#ActivitySelector" ).val();
					          }, places: function(){
					            return $( "#newPeriodsubscriptionPlaces" ).val();
					          }
					        }
					      }},
					NewPeriodStart'. $period['id'] .':{
						required : true},
					NewPeriodEnd'. $period['id'] .':{
						required : true}
				},
				messages:{
					newPeriodsubscriptionPlaces:{ required: "Ce champ est obligatoire!"},
					NewPeriodStart'. $period['id'] .':{ required : "Ce champ est obligatoire!"},
					NewPeriodEnd'. $period['id'] .':{ required : "Ce champ est obligatoire!"}
				}
				});
			</script>';
	}

	public function adminNewForm(){
		parent::testRequest();
		$activities = new Activities();
		return '<form id="form">
					<div class="inputs">'.
						Helper::listWeekDays() . $activities->listActivities()
						.'Nombre de places réservées pour les abonnements:
						<input id="newPeriodsubscriptionPlaces" name="newPeriodsubscriptionPlaces" type="number" placeholder="Nombre de places réservées pour les abonnements" required>
						Heure de début:
						<input id="PeriodStart" name="PeriodStart" class="hasWickedpicker" type="text" placeholder="Heure de début" required readonly>
						Heure de fin:
						<input id="PeriodEnd" name="PeriodEnd" class="hasWickedpicker" type="text" placeholder="Heure de fin" required readonly>
					</div>
				</form>
				<script>
                initTime("#PeriodStart");
                initTime("#PeriodEnd");
				$("#form").validate({
					rules:{
					newPeriodsubscriptionPlaces:{
							required: true,
							remote: {
						        url: "php/schedules/checkMaxPlaces.php",
						        type: "post",
						        data: {
						          activityId: function() {
						            return $( "#ActivitySelector" ).val();
						          }, places: function(){
						            return $( "#newPeriodsubscriptionPlaces" ).val();
						          }
						        }
						      }},
						PeriodStart:{
							required : true},
						PeriodEnd:{
							required : true}
					},
					messages:{
						newPeriodsubscriptionPlaces:{ required: "Ce champ est obligatoire!"},
						PeriodStart:{ required : "Ce champ est obligatoire!"},
						PeriodEnd:{ required : "Ce champ est obligatoire!"}
					}
					});
				</script>';
	}

	/**
	 * @param $schedule PDOStatement
	 * @return array
	 */
	public function createObject($schedule){
		$actualDayId = null;
		$scheduleObject = [];
		while($schedulePeriod = $schedule->fetch()){
			if($actualDayId != $schedulePeriod['dayId']){
				$actualDayId = $schedulePeriod['dayId'];
				$scheduleObject[$actualDayId] = [
					'title' => $schedulePeriod['dayTitle'],
					'dayId' => $schedulePeriod['dayId'],
					'periods' => []
				];
			}
			if($schedulePeriod[ 'periodId' ] && $schedulePeriod[ 'activityId' ] && $schedulePeriod[ 'start' ]
				&& $schedulePeriod[ 'end' ]&& $schedulePeriod[ 'places' ]){
				$scheduleObject[$actualDayId]['periods'][] = [
					'id' => $schedulePeriod[ 'periodId' ],
					'activityId' => $schedulePeriod[ 'activityId' ],
					'places' => $schedulePeriod[ 'places' ],
					'units' => $schedulePeriod[ 'units' ],
					'start' => $schedulePeriod[ 'start' ],
					'end' => $schedulePeriod[ 'end' ]
				];
			}
		}
		foreach( $scheduleObject as $key => $val ){
			orderBy($scheduleObject[$key][ 'periods' ],'start');
		}
		return $scheduleObject;
	}

	public function formatDay( $day, $isForSubscriptions = false){
		$html = "";
		$html .= $this->createEmptyDayUntil($day['dayId']-1);
		$html .= '<div id="'. $day['title'] .'" class="day">';
		$html .= '<div class="dayTitle">'. strtoupper($day['title']) .'</div>';
		$html .= '<div class="periods">';
		foreach($day['periods'] as $period){
			$html .= $this->formatPeriod($period, $isForSubscriptions);
		}
		$html .= '</div>';
		$html .= '</div>';
		$this->actualDay++;
		return $html;
	}

	public function formatPeriod( $period, $isForSubscriptions = false){
		$module = new Activities();
		$activityObject = $module->createObject($module->getOne($period['activityId']));
		if($isForSubscriptions){
			$registrations = new Registrations();
			if($registrations->isPeriodSubscriptionFull($period['id'])){
				$html = '<div id="Period' . $period[ 'id' ] . '" class="period" style="' . $this->getGradient($activityObject[ 'types' ]) . '">';
				$html .= '<div class="periodTitle">'. $activityObject['title'] .'</div>';
				$html .= '<div class="periodHours">' . $this->getTime($period[ 'start' ]) . ' à ' . $this->getTime($period[ 'end' ]) . '</div>';
				$html .= '<div class="periodTitle expired">Pleine!</div>';
			}else{
				$html = '<div id="Period' . $period[ 'id' ] . '" class="period" onclick="selectPeriod(' . "'" . $period[ 'id' ] . "'" . ')" style="' . $this->getGradient($activityObject[ 'types' ]) . '">';
				$html .= '<div class="periodTitle">'. $activityObject['title'] .'</div>';
				$html .= '<div class="periodHours">' . $this->getTime($period[ 'start' ]) . ' à ' . $this->getTime($period[ 'end' ]) . '</div>';
			}
		}else{
			$html = '<div id="Period' . $period[ 'id' ] . '" class="period" onclick="editPeriod(' . "'" . $period[ 'id' ] . "'" . ')" style="' . $this->getGradient($activityObject[ 'types' ]) . '">';
			$html .= '<div class="periodTitle">'. $activityObject['title'] .'</div>';
			$html .= '<div class="periodHours">' . $this->getTime($period[ 'start' ]) . ' à ' . $this->getTime($period[ 'end' ]) . '</div>';
		}
		$html .= '</div>';
		return $html;
	}

	private function getTime($time){
		$timestamp = strtotime($time);
		return date("H:i", $timestamp);
	}

	private function getGradient($types){
		$nbOfColors = count($types);
		$step = 100;
		if($nbOfColors > 2){
			$step = 100 / ($nbOfColors -1);
		}
		$style = 'background:' . $types[0]['color'] . ' !important;'; //old browsers
		$colors = [];
		$actualStep = 0;
		foreach($types as $type){
			$colors[] = $type['color']. ' ' . $actualStep . '%';
			$actualStep += $step;
		}
		$colors = implode(', ', $colors);
		$style .= 'background: -moz-linear-gradient(top,'. $colors . ') !important;';
		$style .= 'background: -webkit-linear-gradient(top,'. $colors . ') !important;';
		$style .= 'background: linear-gradient(to bottom,'. $colors . ') !important;';
		return $style;
		//background: -moz-linear-gradient(top, rgb(254,204,177) 0%, rgb(241,116,50) 50%, rgb(234,85,7) 50%, rgb(251,149,94) 100%); /* FF3.6-15 */
        //background: -webkit-linear-gradient(top, rgb(254,204,177) 0%,rgb(241,116,50) 50%,rgb(234,85,7) 50%,rgb(251,149,94) 100%); /* Chrome10-25,Safari5.1-6 */
        //background: linear-gradient(to bottom, rgb(254,204,177) 0%,rgb(241,116,50) 50%,rgb(234,85,7) 50%,rgb(251,149,94) 100%);
	}

	private function createEmptyDayUntil($dayId){
		$html ='';
		if($this->actualDay == 0 && $dayId != 0){
			$this->actualDay++;
		}
		while( $this->actualDay < $dayId ){
			$html .= '<div id="' . Helper::$days[ $this->actualDay ] . '" class="day">';
			$html .= '<div class="dayTitle">' . strtoupper(Helper::$days[ $this->actualDay ]) . '</div>';
			$html .= '<div class="emptyPeriods">';
			$html .= '</div>';
			$html .= '</div>';
			$this->actualDay++;
		}
		return $html;
	}

	public function memberFormat( $schedule ){
		parent::testRequest();
		$scheduleObject = $this->createObject($schedule);
		$html = "";
		foreach($scheduleObject as $day){
			$html .= $this->formatDay($day, true);
		}
		$html .= $this->createEmptyDayUntil(7);
		return $html;
	}

	public function formatPeriodTitle($periodId){
		$period = $this->getOne($periodId)->fetch();
		$activities = new Activities();
		$activityObject = $activities->createObject($activities->getOne($period['activity_id']));
		return $activityObject['title'] . ' les '. Helper::$days[$period['day_id']-1] . ' à ' . formatTime($period['start']);
	}
}