<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Meetings extends Modules
{
	private $fields = 'sessions.id as sessionId, p.id as periodId, d.id as dayId, d.title as dayTitle,
						m.id as meetingId, m.start, m.end, ac.id as activityId, units, places, week,
						m.active as meetingWeekActive, subscription_places';
	private $customTable = 'sessions
					 LEFT JOIN periods as p on sessions.id = p.session_id
					 LEFT JOIN activities as ac on p.activity_id = ac.id
					 LEFT JOIN (
						SELECT activity_id, type.title, min(places) as places
					    FROM activities_activity_types as actypes
						LEFT JOIN activity_types as type on actypes.activity_type_id = type.id group by actypes.activity_id order by places asc) as types
					 on p.activity_id = types.activity_id
					 LEFT JOIN days as d on d.id = p.day_id
		             LEFT JOIN meetings as m on p.id = m.period_id';
	/**
	 * Meetings constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('meetings');
	}

	public function getOne( $meetingId, $usingActive = true, $debug = false ){
		return DB::getInstance()->select($this->customTable,$this->fields,'m.id= '. $meetingId . ' AND p.active = true order by dayId', false);
	}

	public function getPeriodMeetings($periodId){
		return DB::getInstance()->select($this->customTable,$this->fields,'p.id= '. $periodId . ' AND p.active = true AND m.active = true order by week', false);
	}

	public function getPeriodMeetingsLeftRatio($periodId){
		$periodMeetings = $this->getPeriodMeetings($periodId);
		$totalMeetings = $periodMeetings->rowCount();
		$leftMeetings = 0;
		while($periodMeeting = $periodMeetings->fetch()){
			if(!$this->isStarted($periodMeeting['meetingId'])){
				$leftMeetings++;
			}
		}
		return $leftMeetings/$totalMeetings;
	}

	public function format( $meeting){
		parent::testRequest();
		fail();
	}

	public function adminEditForm( $meeting){
		parent::testRequest();
		fail();
	}

	public function adminNewForm(){ //special meetings form
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
	 * @return array
	 */
	public function getAllFutureMeetings(){
		$futureMeetings = [];
		$meetings = $this->getAll();
		$currentDateTime = time();
		while($meeting = $meetings->fetch()){
			if($currentDateTime < $this->getDateTime($meeting['id'])->getTimestamp()){
				$futureMeetings[] = $meeting;
			}
		}
		return $futureMeetings;
	}

	public function getMaxPlaces($meetingId){
		$meeting = $this->getOne($meetingId)->fetch();
		return $meeting['places'];
	}

	public function getMaxSubscriptionPlaces($meetingId){
		$meeting = $this->getOne($meetingId)->fetch();
		return $meeting['subscription_places'];
	}

	public function getSessionId($meetingId){
		$meeting = $this->getOne($meetingId)->fetch();
		return $meeting['sessionId'];
	}

	public function isPeriodMix($periodId){
		$meeting = $this->getPeriodMeetings($periodId)->fetch();
		return $meeting['units'] >1;
	}

	public function isActive($meetingId){
		$meeting = $this->getOne($meetingId)->fetch();
		return $meeting['meetingWeekActive'];
	}

	public function isRegrationLimitPassed($meetingId, $receiptId){
		$now = time();
		$purchases = new Purchases();
		$purchase = $purchases->getOne($receiptId)->fetch();
		$meetingDateTime = $this->getDateTime($meetingId);
		$offset = $this->getTimeOffset($purchase['registration_deadline']);
		return $now > ($meetingDateTime->getTimestamp() - $offset);
	}

	public function isCancellationLimitPassed($meetingId, $receiptId){
		$now = time();
		$purchases = new Purchases();
		$purchase = $purchases->getOne($receiptId)->fetch();
		$meetingDateTime = $this->getDateTime($meetingId);
		$offset = $this->getTimeOffset($purchase['meetingCancelDeadline']);
		return $now > ($meetingDateTime->getTimestamp() - $offset);
	}

	public function isStarted($meetingId){
		$now = time();
		$meetingDateTime = $this->getDateTime($meetingId);
		return $now > ($meetingDateTime->getTimestamp());

	}

	public function isRegistrationsOpen( $meetingId, $userId){
		$now = time();
		$canTryToRegister = false;
		$purchases = new Purchases($userId);
		$userPurchases = $purchases->getAll();
		$meetingDateTime = $this->getDateTime($meetingId);
		$hasPurchases = false;
		while($purchase = $userPurchases->fetch()){
			$hasPurchases = true;
			if($meetingDateTime->getTimestamp() <= $now + ($purchase['limit_registration_advance'] * WEEK_STAMP)){
				$canTryToRegister = true;
			}
		}
		if(!$hasPurchases){
			alert('Afin de pouvoir vous inscrire à une séance, vous devez préalablement vous procurer un forfait! <br>
			<button class="button edit" onclick=' . "'hideCustom();redirect(\"members.php?m=Purchases\")'" . '>Voir mes forfaits</button>
			');
		}
		return $canTryToRegister;
	}

	/**
	 * @param $meetingId int
	 * @return DateTime
	 */
	public function getDateTime($meetingId){
		$sessions = new Sessions();
		$meeting = $this->getOne($meetingId)->fetch();
		$session = $sessions->getOne($meeting['sessionId'])->fetch();
		$sessionsStartTimeStamp = strtotime($session['start_date']);
		$dayTimeStamp = ($meeting['dayId']-1) * DAY_STAMP; // -1 for array and -1 for day_Stamp Issue
		$weekTimeStamp = ($meeting['week']-1) * WEEK_STAMP;
		$meetingDate = new DateTime();
		$meetingDate->setTimestamp($sessionsStartTimeStamp + $weekTimeStamp + $dayTimeStamp);
		//You can't simply do "createFromFormat(H:i:s.u', $meeting['start'])" because it need to have a date too...
		//So by default, it was setting the actual day as Date and adding the time of $meeting['start']...
		$finalDateTime = DateTime::createFromFormat( 'Y-m-d H:i:s.u', $meetingDate->format('Y-m-d') . ' '. $meeting['start']);
		return $finalDateTime;
	}

	public function formatMeetingTitleTime($meetingId){
		$meeting = $this->getOne($meetingId)->fetch();
		$module = new Activities();
		$activityObject = $module->createObject($module->getOne($meeting['activityId']));
		$dateTime = $this->getDateTime($meetingId);
		return $activityObject['title'] . ' le ' . formatDateTime($dateTime, false, true, true);
	}

	public function getTimeOffset($mysqlTimeString){
		$todayWithOffset = DateTime::createFromFormat( 'H:i:s.u',$mysqlTimeString);
		$timestampOffset = $todayWithOffset->getTimestamp();
		$todayNoTime = DateTime::createFromFormat( 'Y-m-d H:i:s.u',date("Y-m-d") . ' 00:00:00.00000');
		$timestamp = $todayNoTime->getTimestamp();
		return $timestampOffset - $timestamp;
	}

	public function getCost($meetingId){
		$meeting = $this->getOne($meetingId)->fetch();
		return floatval($meeting['units']);
	}

	public function dumpTime($timestamp){
		var_dump(date('Y-m-d H:i:s.u', $timestamp));
	}
}