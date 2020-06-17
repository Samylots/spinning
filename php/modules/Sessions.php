<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Sessions extends Modules implements MemberFormatter
{
	/**
	 * Sessions constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('sessions');
		$this->adminToolbar->addOption('Nouvelle session', null, 'newSession()', $this->adminToolbar->hasToBeActive(['getSessions.php']));
	}

	public function getAll(){
		try {
			return DB::getInstance()->select($this->table, '*', 'order by start_date');
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}


	public function format( $session){
		parent::testRequest();
		/*$currentDate = date('Y-m-d');
		$startDate = date('Y-m-d', strtotime($session['start_date']));*/
		return '<span id="sessionInfo" class="info">
			<span class="title">'.
		$session['title']
		.'</span>
			<span class="start">Du '.
		formatDateTime($session['start_date'])
		.'</span>
			<span class="end"> au '.
			formatDateTime($session['end_date'])
		.'</span>
			<span class="total"> ('.
		plurialNoun($session['total_weeks'], 'semaine')
		.')</span>
		</span>
		<span id="sessionAction" class="actions">
			<button class="button confirm" onclick='. "'manageSession(\"". $session['id'] ."\")'" .'>Gérer</button>
			<button class="button edit" onclick='. "'editSession(\"". $session['id'] ."\")'". '>Modifier</button>
			<button class="button cancel" onclick='. "'deleteSession(\"". $session['id'] ."\")'" .'>Supprimer</button>
		</span>';
		//($currentDate > $startDate ? 'disabled' : '') .
	}

	public function adminEditForm( $session){
		parent::testRequest();
		$currentDate = date('Y-m-d');
		$startDate = date('Y-m-d', strtotime($session['start_date']));
		return '<form id="form'. $session['id'] .'">
					<span id="sessionInfo'. $session['id'] .'" class="info">'.
						'Titre:
						<input '. getIdName('newSessionTitle'. $session['id']).' type="text" class="title" placeholder="Titre de la session" value="'. htmlspecialchars ($session['title']) .'" required">
						Première semaine (date de début):
						<input '. getIdName('newSessionStart'. $session['id']).' type="text" class="start" placeholder="Première semaine" '. ($currentDate > $startDate ? 'disabled' : '') .' value="'. $session['start_date'] .'" readonly required>
						Dernière semaine (date de fin):
						<input '. getIdName('newSessionEnd'. $session['id']).' type="text" class="end" placeholder="Dernière semaine" '. ($currentDate > $startDate ? 'disabled' : '') .' value="'. $session['end_date'] .'" readonly required>
						Date de libération des places réservées aux abonnés:
						<input '. getIdName('newSessionSubscriptionPlaces'. $session['id']).' type="text" class="end" placeholder="Date de libération des places réservées aux abonnés" value="'. $session['subscription_places_date'] .'" readonly required>'.
					'</span>
					<span id="sessionAction'. $session['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditSession(\"" . $session['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditSession(\"" . $session['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
					today = new Date();
		            nextWeek = today - today.getDay()*DAY_STAMP + WEEK_STAMP;
		            console.log(new Date(nextWeek));'.
					($currentDate< $startDate ?
						'initWeekPickers("#newSessionStart'. $session['id'] .'", "#newSessionEnd'. $session['id'] .'",  new Date(nextWeek));
			            $("#newSessionStart'. $session['id'] .'").on("dateSelected", function( event, date ) {
			                $("newSessionSubscriptionPlaces'. $session['id'] .'").datepicker( "option", "minDate", date);
			            });
			            $("#newSessionEnd'. $session['id'] .'").on("dateSelected", function( event, date ) {
			                $("newSessionSubscriptionPlaces'. $session['id'] .'").datepicker( "option", "maxDate", date);
			            });' : '') .
	                'initDatePicker("#newSessionSubscriptionPlaces'. $session['id'] .'")
	                var min = new Date($("#newSessionStart'. $session['id'] .'").val());
	                var max = new Date($("#newSessionEnd'. $session['id'] .'").val());
	                $("#newSessionSubscriptionPlaces'. $session['id'] .'").datepicker( "option", "minDate",  min)
	                $("#newSessionSubscriptionPlaces'. $session['id'] .'").datepicker( "option", "maxDate",  max);
					$("#form'. $session['id'] .'").validate({
						rules:{
							newSessionTitle'. $session['id'] .':{ minlength : 3,
							required: true},
						},
						messages:{
							newSessionTitle'. $session['id'] .':{ minlength: "Le titre doit être au moins de 3 caractères de long.",
							required: "Ce champ est obligatoire!"},
						}
					});
				</script>';

	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
						Titre:
						<input id="SessionTitle" name="SessionTitle" type="text" placeholder="Titre de la session" required>
						Première semaine (date de début):
						<input id="SessionStart" name="SessionStart" type="date" class="start" value="" readonly placeholder="Première semaine" required>
						Dernière semaine (date de fin):
						<input id="SessionEnd" name="SessionEnd" type="date" class="end" value="" readonly placeholder="Dernière semaine" required>
						Date de libération des places réservées aux abonnés:
						<input '. getIdName('SessionSubscriptionPlaces') .' type="text" class="end" placeholder="Date de libération des places réservées aux abonnés" value="" readonly required>
					</div>
				</form>
				<script>
					today = new Date();
		            nextWeek = today - today.getDay()*DAY_STAMP + WEEK_STAMP;
    				initWeekPickers("#SessionStart","#SessionEnd", new Date(nextWeek));
    				initDatePicker("#SessionSubscriptionPlaces");
		            $("#SessionStart").on("dateSelected", function( event, date ) {
		                $("SessionSubscriptionPlaces").datepicker( "option", "minDate", date);
		            });
		            $("#SessionEnd").on("dateSelected", function( event, date ) {
		                $("SessionSubscriptionPlaces").datepicker( "option", "maxDate", date);
		            });
					$("#form").validate({
						rules:{
							SessionTitle:{ minlength : 3}
						},
						messages:{
							SessionTitle:{ minlength: "Le titre doit être au moins de 3 caractères de long."},
						}
					});
				</script>';
	}

	public function getNbOfWeeks($start,$end){
		$dateDiff = strtotime($end) - strtotime($start);
		return ceil($dateDiff/WEEK_STAMP);
	}

	public function getTotalWeeks($id){
		$session = $this->getOne($id)->fetch();
		return $session['total_weeks'];
	}

	public function getActualWeek($id){
		$session = $this->getOne($id)->fetch();
		if($session['start_date'] < now()){
			return $this->getNbOfWeeks($session[ 'start_date' ], now());
		}else if(now() < $session['start_date']){
			return 1;
		}else if(now() > $session['end_date']){
			return $session['total_weeks'];
		}else{
			return false;
		}
	}

	public function getWeekTitle($sessionId, $weekId){
		$sessionWeekModule = new WeekSchedules($sessionId, $weekId);
		$firstDayId = $sessionWeekModule->getFirstDayIdOfWeek();
		$weekId--;
		$session = $this->getOne($sessionId)->fetch();
		$startDate = new DateTime($session['start_date']);
		$weekStartDate = new DateTime();
		$weekEndDate = new DateTime();
		$weekStartDate->setTimestamp($startDate->getTimestamp() + ($weekId * WEEK_STAMP) + ($firstDayId == 1 ? DAY_STAMP : 0));
		$weekEndDate->setTimestamp($startDate->getTimestamp() + ($weekId * WEEK_STAMP) + WEEK_STAMP - DAY_STAMP*2);
		return 'SEMAINE ' . ($weekId+1) . ' (DU ' . formatDateTime($weekStartDate->format('Y-m-d'),true,false,true). ' AU ' . formatDateTime($weekEndDate->format('Y-m-d'),true,false,true) . ')';
	}

	public function getCorrectedWeek($sessionId, $weekId){
		$session = $this->getOne($sessionId)->fetch();
		if($weekId > $session['total_weeks']){
			return $session['total_weeks'];
		}else if($weekId < 0){
			return 1;
		}else{
			return $weekId;
		}
	}

	public function getPeviousWeek($sessionId, $weekId){
		return $this->getCorrectedWeek($sessionId, $weekId-1);
	}

	public function getNextWeek($sessionId, $weekId){
		return $this->getCorrectedWeek($sessionId, $weekId+1);
	}

	public function getAvailableSessions(){
		$availableSessions =[];
		$sessions = $this->getAll();
		$currentDate = date('Y-m-d');
		foreach( $sessions as $session ){
			$endDate = date('Y-m-d', strtotime($session['end_date']));
			if($currentDate < $endDate){
				$availableSessions[] = $session;
			}
		}
		return $availableSessions;
	}

	public static function formatTitle($session, $showDates = true){
		return $session['title']. ($showDates ? ' (Du '. formatDateTime($session['start_date']).' au '. formatDateTime($session['end_date']) .')' : '');
	}

	public function getNearestSession(){
		$nearestSession = null;
		$sessions = $this->getAll();
		$currentDate = date('Y-m-d');
		foreach( $sessions as $session ){
			$endDate = date('Y-m-d', strtotime($session['end_date']));
			if($currentDate < $endDate){
				if($nearestSession == null){
					$nearestSession = $session;
				}else if(date('Y-m-d', strtotime($session['end_date'])) < date('Y-m-d', strtotime($nearestSession['end_date']))){
					$nearestSession = $session;
				}
			}
		}
		return $nearestSession;
	}

	public function memberFormat( $session ){
		parent::testRequest();
		return '<span id="sessionInfo" class="info">
			<span class="title">'.
		$session['title']
		.'</span>
			<span class="start">Du '.
		formatDateTime($session['start_date'])
		.'</span>
			<span class="end"> au '.
		formatDateTime($session['end_date'])
		.'</span>
			<span class="total"> ('.
		plurialNoun($session['total_weeks'], 'semaine')
		.')</span>
		</span>
		<span id="sessionAction" class="actions">
			<button class="button edit" onclick="'. "showSchedule('". $session['id'] ."')\"". '>Consulter l\'horaire</button>
		</span>';
	}

	public function canCardsOverrideSubscriptionsPlaces($sessionId){
		$session = $this->getOne($sessionId)->fetch();
		return strtotime($session['subscription_places_date']) < time();
	}
}