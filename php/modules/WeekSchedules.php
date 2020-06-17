<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-05
 * Time: 11:19
 */
class WeekSchedules extends Modules implements MemberFormatter
{
	private $actualDay = 0;
	private $actualSessionId;
	private $actualWeekId;
	private $coaches;
	private $user;

	private $registrationModule;
	private $fields = 'sessions.id as sessionId, p.id as meetingId, d.id as dayId, d.title as dayTitle,
						m.id as meetingId, m.start, m.end, ac.id as activityId, units, places, subscription_places,
						week, m.active as meetingWeekActive';
	private $customTable = 'sessions
			 LEFT JOIN periods as p on sessions.id = p.session_id
			 LEFT JOIN activities as ac on p.activity_id = ac.id
			 LEFT JOIN (
				SELECT activity_id, type.title, min(places) as places
			    FROM activities_activity_types as actypes
				LEFT JOIN activity_types as type on actypes.activity_type_id = type.id
			    group by actypes.activity_id order by places asc) as types
			 on p.activity_id = types.activity_id
			 LEFT JOIN days as d on d.id = p.day_id
             LEFT JOIN meetings as m on p.id = m.period_id';
	/**
	 * Schedules constructor.
	 * @param string $table
	 */
	function __construct( $sessionId, $weekId = 1) {
		$this->actualSessionId = $sessionId;
		$this->actualWeekId = $weekId;
		$this->registrationModule = new Registrations();
		$this->coaches = new Coaches();
		$this->user = new User();
		parent::__construct('meetings');
		//$this->adminToolbar->addOption('Nouvelle séance spéciale', null, 'newMeeting()', $this->adminToolbar->hasToBeActive(['getSessionWeek.php']));
	}

	public function getAll(){
		return DB::getInstance()->select($this->customTable, $this->fields,'sessions.id = '. $this->actualSessionId .' AND week = '. $this->actualWeekId .' AND p.active = true order by dayId', false);
	}

	public function getOne( $id, $usingActive = true, $debug = false ){
		return DB::getInstance()->select($this->customTable, $this->fields,'sessions.id = '. $this->actualSessionId .' AND m.id= '. $id . ' AND p.active = true order by dayId', false);
	}

	public function format( $schedule){
		parent::testRequest();
		$scheduleObject = $this->createObject($schedule);
		$html = "";
		foreach($scheduleObject as $day){
			$html .= $this->formatDay($day, true);
		}
		$html .= $this->createEmptyDayUntil(7);
		return $html;
	}

	public function getFirstDayIdOfWeek(){
		$scheduleObject = $this->createObject($this->getAll());
		if(!empty($scheduleObject)){
			return array_values($scheduleObject)[ 0 ][ 'dayId' ]- 1;
		}else{
			return 1;
		}
	}


	public function adminEditForm( $meeting){
		parent::testRequest();
		$meetings = new Meetings();
		$coaches = new Coaches($meeting['meetingId']);
		return '<form id="form'. $meeting['meetingId'] .'">'.
					Helper::tip($meetings->formatMeetingTitleTime($meeting['meetingId'])).
					'<div id="scheduleInfo'. $meeting['meetingId'] .'" class="inputs">'.
						'Heure de début:
						<input id="NewMeetingStart" name="NewMeetingStart" class="hasWickedpicker" type="text" placeholder="Heure de début" required readonly value="'. $meeting['start'] .'">
						Heure de fin:
						<input id="NewMeetingEnd" name="NewMeetingEnd" class="hasWickedpicker" type="text" placeholder="Heure de début" required readonly value="'. $meeting['end'] .'">
						séance annulé?:
						<input type="checkbox" id="NewMeetingActive" name="NewMeetingActive" '. ($meeting['meetingWeekActive'] ? '' : ' checked') .'></br>
						Entraîneur: <button class="button confirm" onclick=' . "'addCoach(\"" . $meeting['meetingId'] . "\")'" . '>Assigner un entraîneur</button>
						<div id="coaches" class="item">'.
							$coaches->listActualCoaches(true).
						'</div>
					</div>'.
				'</form>
				<script>
	                initTime("#NewMeetingStart","'. $meeting['start'] .'");
		            initTime("#NewMeetingEnd","'. $meeting['end'] .'");
					$("#form'. $meeting['meetingId'] .'").validate({
						rules:{
							newMeetingsubscriptionPlaces:{
								required : true,
								min:0,
								max:'. $meeting['places'] .'
							},
							NewMeetingStart:{
								required : true},
							NewMeetingEnd:{
								required : true}
						},
						messages:{
							newMeetingsubscriptionPlaces:{
								required : "Vous devez obligatoirement indiquer le nombre de places réservées pour les abonnements! ('. plurialNoun($meeting['places'], 'place disponible') .')",
								min: "Il est impossible de réserver un nombre négatif de places!",
								max: "Il est impossible de réserver plus places que ce qu\'il est disponible! ('. plurialNoun($meeting['places'], 'places') .')"
							},
							NewMeetingStart:{ required : "Ce champ est obligatoire!"},
							NewMeetingEnd:{ required : "Ce champ est obligatoire!"}
						}
					});
				</script>';
	}

	public function adminNewForm(){
		parent::testRequest();
		fail('CANT CREATE HERE');
	}

	/**
	 * @param $schedule PDOStatement
	 * @return array
	 */
	public function createObject($schedule){
		$actualDayId = null;
		$scheduleObject = [];
		while($scheduleMeeting = $schedule->fetch()){
			if($actualDayId != $scheduleMeeting['dayId']){
				$actualDayId = $scheduleMeeting['dayId'];
				$scheduleObject[$actualDayId] = [
					'title' => $scheduleMeeting['dayTitle'],
					'dayId' => $scheduleMeeting['dayId'],
					'meetings' => []
				];
			}
			if($scheduleMeeting[ 'meetingId' ] && $scheduleMeeting[ 'activityId' ] && $scheduleMeeting[ 'start' ]
				&& $scheduleMeeting[ 'end' ]&& $scheduleMeeting[ 'places' ]){
				$scheduleObject[$actualDayId]['meetings'][] = [
					'id' => $scheduleMeeting[ 'meetingId' ],
					'meetingId' => $scheduleMeeting[ 'meetingId' ],
					'activityId' => $scheduleMeeting[ 'activityId' ],
					'places' => $scheduleMeeting[ 'places' ],
					'subscriptionPlaces' => $scheduleMeeting[ 'subscription_places' ],
					'units' => $scheduleMeeting[ 'units' ],
					'start' => $scheduleMeeting[ 'start' ],
					'end' => $scheduleMeeting[ 'end' ],
					'active' => $scheduleMeeting[ 'meetingWeekActive' ]
				];
			}
		}
		foreach( $scheduleObject as $key => $val ){
			orderBy($scheduleObject[$key][ 'meetings' ],'start');
		}
		return $scheduleObject;
	}

	public function formatDay( $day, $isPublic){
		$html = "";
		$html .= $this->createEmptyDayUntil($day['dayId'] -1);
		$html .= '<div id="'. $day['title'] .'" class="day">';
		$html .= '<div class="dayTitle">'. strtoupper($day['title']) .'</div>';
		$html .= '<div class="meetings">';
		foreach($day['meetings'] as $meeting){
			$html .= $this->formatMeeting($meeting, $isPublic);
		}
		$html .= '</div>';
		$html .= '</div>';
		$this->actualDay++;
		return $html;
	}

	public function formatMeeting( $meeting, $isAdminPanel){
		$this->coaches->setMeetingId($meeting[ 'id' ]);
		$module = new Activities();
		$activityObject = $module->createObject($module->getOne($meeting['activityId']));
		if($isAdminPanel){
			$html = '<div id="Meeting' . $meeting[ 'id' ] . '" class="meeting" onclick="editMeeting(' . "'" . $meeting[ 'id' ] . "'" . ')" style="' . $this->getGradient($activityObject[ 'types' ]) . '">';
		}else{
			$html = '<div id="Meeting' . $meeting[ 'id' ] . '" class="meeting" onclick="showMeeting(' . "'" . $meeting[ 'id' ] . "'" . ')" style="' . $this->getGradient($activityObject[ 'types' ]) . '">';
			if($this->user->isLogged() && $this->registrationModule->isRegistered($this->user->getUserId(),$meeting['id'])){
				$html .= '<div class="memberRegistrationCheck">
							<img src="img/site/check25.png" alt="Inscrit" />
						</div>';
			}
			$html .= '<script> setList('. $meeting[ 'id' ] .'); </script>';
		}
		if($meeting['active']){
			$html .= '<div class="meetingTitle">' . $activityObject[ 'title' ] . (!$isAdminPanel ? $this->registrationModule->getPlacesTag($meeting['id']) : '') . '</div>';
			$html .= '<div class="meetingHours">' . $this->getTime($meeting[ 'start' ]) . ' à ' . $this->getTime($meeting[ 'end' ]) . '</div>';
			if($this->user->isLogged()){
				$html .= '<div class="meetingCoach">' . $this->coaches->getCoachName() . '</div>';
			}
		}else{
			$html .= '<div class="exception">Séance annulée</div>
					 <div class="exception">exceptionnellement</div>
					 <div class="exception"> cette semaine</div>';
		}
		$html .= '</div>';
		return $html;
	}

	public function setWeek($weekId){
		$this->actualWeekId = $weekId;
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

	public function memberFormat( $schedule ) {
		parent::testRequest();
		$scheduleObject = $this->createObject($schedule);
		$html = "";
		foreach($scheduleObject as $day){
			$html .= $this->formatDay($day, false);
		}
		$html .= $this->createEmptyDayUntil(7);
		return $html;
	}
}