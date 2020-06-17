<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-13
 * Time: 19:29
 */
class Registrations extends Modules
{
	private $registeredMeetingOfMember;
	private static $actualMember;
	/**
	 * Registrations constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('meetings_subscription_receipts');
	}

	public function getMeetingRegistrations($meetingId){
		$queryString = 'SELECT mr.id, r.id as receiptId, mr.meeting_id as meetingId, mr.name, mr.active, mr.waiting, mr.registred,
						t.weekly FROM ' . $this->table . ' as mr inner join subscription_receipts as r on
						mr.subscription_receipt_id = r.id inner join subscription_types as t on r.type_id = t.id
						where r.active = true AND mr.active = true AND  mr.meeting_id='. $meetingId . ' order by mr.waiting, registred';
		return DB::getInstance()->customQuery($queryString);
	}

	public function getReceiptResgistrations($receiptId){
		$queryString = 'SELECT * FROM ' . $this->table . ' WHERE subscription_receipt_id=' . $receiptId . ' order by waiting, registred';
		return DB::getInstance()->customQuery($queryString);
	}

	public function getReceiptMeetingRegistrations($receiptId, $meetingId){
		$queryString = 'SELECT * FROM ' . $this->table . ' WHERE active=true and subscription_receipt_id=' . $receiptId . ' and meeting_id='. $meetingId . ' order by waiting, registred';
		return DB::getInstance()->customQuery($queryString);
	}

	public function getMemberRegistredMeetings($memberId){
		$queryString = 'SELECT meeting_id from ' . $this->table . ' as mr inner join subscription_receipts as r
						on mr.subscription_receipt_id = r.id inner join members as m on r.member_id = m.id
						and member_id='. $memberId . ' where mr.active = true and r.active = true order by mr.waiting, registred';
		return DB::getInstance()->customQuery($queryString);
	}

	public function getMemberSubscribedPeriodsWithReceipt($memberId, $receiptId){
		$queryString = 'SELECT period_id from periods_subscription_receipts as pr inner join subscription_receipts as r
						on pr.subscription_receipt_id = r.id inner join periods as p on pr.period_id = p.id inner join members as m on r.member_id = m.id
						and member_id='. $memberId . ' where pr.active = true and r.active = true and r.id = '. $receiptId . ' order by p.day_id';
		return DB::getInstance()->customQuery($queryString);
	}

	public function edit( $id, $data , $debug = false){
		fail();
	}

	public function format( $meeting){
		// TODO SELECT FIRST IN FORFAIT SELECTION
		parent::testRequest();
		$user = new User();
		$members = new Members();
		$meetings = new Meetings();
		$html = '';

		$html .= '<form id="formRegistration">';
		$html .= Helper::tip($meetings->formatMeetingTitleTime($meeting['meetingId']));
		if(!$meetings->isActive($meeting['meetingId'])){
			alert('Desolé, il est impossible de s\'inscrire à une séance annulé!');
		}

		if($user->isAdmin()){
			$html .= $this->listMembersWithPurchases();
			$member = $members->getAllWithPurchases()->fetch();
			$html .= $this->listPurchases($meeting['meetingId'], $member['id']);
		}else{
			$html .= $this->listPurchases($meeting[ 'meetingId' ]);
			$member = $user->getUser();
		}

		if(!$meetings->isRegistrationsOpen($meeting['meetingId'], $member['id'])){
			alert('Désolé, les inscriptions pour cette séance ne sont pas encore ouverte!');
		};

		if($meetings->isStarted($meeting['meetingId'])){
			alert('Désolé, cette séance en cours ou terminée. Les inscriptions sont alors fermées!');
		}

		$html .= 'Au nom de :
				<input '. getIdName('newRegistrationName').' type="text" class="name" placeholder="Nom de la personne" value="'. $members->getFullName($member) .'" required">';

		$coaches = new Coaches($meeting['meetingId']);
		$html .= 'Entraîneur(s) de ce cours:
				<div id="coaches" class="item">'.
				$coaches->listActualCoaches().
				'</div>';

		$html .= 'Inscription(s) personnelle(s):
				<div id="registrations" class="item">'.
					$this->listActualRegistrations($meeting['meetingId'], $member['id']).
				'</div>';
		$html .= '</form>';
		if($user->isAdmin()){
			$html .= '<script>
		            function changeInfo(){
		                var memberId = $("#memberSelector").val();
		                console.log(memberId);
		                var selectedPackage = $("#packageSelector").val();
				        callAjax("php/registrations/getPurchasesOptions.php", {memberId:memberId, meetingId:'.$meeting['meetingId'] .'}, "html", function (html) {
				            tryToReselectAfterChange("#packageSelector", html, selectedPackage);
				        });
		                callAjax("php/members/getMemberFullname.php", {memberId: memberId}, "html", function(html){
		                    $("#newRegistrationName").val(html);
		                })
		                callAjax("php/registrations/listActualRegistrations.php", {memberId: memberId, meetingId:'.$meeting['meetingId'] .'}, "html", function(html){
					        replaceContentOf("registrations", html);
					    });
		            }

	                $("#memberAC").val("'. $members->getFullName($member) .'");
					</script>';
		}
		$html .= '<script>
					$("#formRegistration").validate({
					rules:{
						newRegistrationName:{ required : true, minlength:3}
					},
					messages:{
						newRegistrationName:{ minlength:"Le nom de la personne doit être de 3 caractères de long!"}
					}
					});
				</script>';
		return $html;
	}

	public function adminEditForm( $registration){
		parent::testRequest();
		fail();
	}

	public function adminNewForm(){
		parent::testRequest();
		fail();
	}

	public function deletePeriodRegistrations($periodId){
		$meetings = new Meetings();
		$periodMeetings = $meetings->getPeriodMeetings($periodId);
		while($meeting = $periodMeetings->fetch()){
			if(!$meetings->isStarted($meeting['meetingId'])){
				$registrations  = $this->getMeetingRegistrations($meeting['meetingId']);
				while($registration = $registrations->fetch()){
					$this->deepDelete($registration['id']);
				}
			}
		}
	}

	public function deepDelete( $registrationId ){
		$meetings = new Meetings();
		$registration = $this->getOne($registrationId)->fetch();
		$purchasesModule = new Purchases();
		$purchasesModule->gain($registration['subscription_receipt_id'], $meetings->getCost($registration['meeting_id']));
		return parent::deepDelete($registrationId);
	}

	public function delete( $registrationId ){
		$registration = $this->getOne($registrationId)->fetch();
		$purchases = new Purchases();
		$purchase = $purchases->getOne($registration['subscription_receipt_id'])->fetch();
		if($purchase['weekly']){
			$result = DB::getInstance()->select('credits','*','subscription_receipt_id = '. $registration['subscription_receipt_id'] . ' and active = true');
			if($result){
				if($result['total_credits'] >=3){
					DB::getInstance()->update($result['id'],'credits',[
						'total_credits' => 0
					]);
					$purchases->edit($registration['subscription_receipt_id'],[
						'meetings_left' => $registration['meetings_left'] + 1
					]);
				}else{
					DB::getInstance()->update($result['id'],'credits',[
						'total_credits' => $result['total_credits']+1
					]);
				}
			}else{
				DB::getInstance()->add('credits',[
					'total_credits' => 1,
					'subscrtiption_receipt_id' => $registration['subscription_receipt_id']
				]);
			}
			parent::delete($registrationId);
		}else{
			parent::delete($registrationId);
		}
	}


	public function registerMember($meetingId, $receiptId, $name){
		$meetings = new Meetings();
		$purchasesModule = new Purchases();
		$purchase = $purchasesModule->getOne($receiptId)->fetch();
		$canRegister = false;
		if($this->isMeetingFull($meetingId)){
			$canRegister = true; //waiting list
		}else{
			if ( $purchase[ 'weekly' ] ){
				if ( $this->canSubscribeToMeeting($meetingId) ){
					if ( $this->isWeekFullForSubscription($meetingId, $receiptId) ){
						alert('Vous avez déjà utilisé les séances diponibles pour cette semaine. <br> Veuillez choisir une séance dans une autre semaine!');
					} else{
						$canRegister = true;
					}
				}
			} else{
				if ( $this->canRegisterToMeeting($meetingId) ){
					$canRegister = true;
				}
			}
		}
		if($canRegister && $purchasesModule->hasEnough($purchase, $meetings->getCost($meetingId))){
			if(!$meetings->isRegrationLimitPassed($meetingId, $receiptId)){
				if ( !$this->isMeetingFull($meetingId) ){
					$purchasesModule->pay($receiptId, $meetings->getCost($meetingId));
					$receiptId = $purchasesModule->getLastUsed(); //Only the last card that paid it!
					$this->add([
						'subscription_receipt_id' => $receiptId,
						'meeting_id' => $meetingId,
						'name' => $name,
						'registred' => now(),
						'waiting' => $this->isMeetingFull($meetingId) //This is for waiting list management later...
					]);
				} else{
					alert('Cette séance est malheureusement déjà pleine!<br> Veuillez en choisir une autre.');
				}
			}else{
				alert('Désolé, le temps limite pour s\'inscrire à cette séance est dépassé!');
			}
		}
	}

	private function isWeekFullForSubscription( $meetingId, $receiptId){
		$purchases = new Purchases();
		$purchase = $purchases->getOne($receiptId)->fetch();
		$meetings = new Meetings();
		$meeting = $meetings->getOne($meetingId)->fetch();
		$week = $meeting['week'];
		$available = $purchase['meetings'];
		$used = 0;
		$registrations = $this->getReceiptResgistrations($receiptId);
		while($registration = $registrations->fetch()){
			$meeting = $meetings->getOne($registration['meeting_id'])->fetch();
			if($meeting['week'] == $week){
				$used++;
			}
		}
		if($used == $available){
			return true;
		}
		return false;
	}

	public function updateRegistrationsOnMeetingStatus($meetingId, $meetingActive){
		if($meetingActive == 'false'){
			$meetings = new Meetings();
			if(!$meetings->isStarted($meetingId)){
				$actualRegistrations = $this->getMeetingRegistrations($meetingId);
				while( $registration = $actualRegistrations->fetch() ){
					$this->deepDelete($registration[ 'id' ]);
				}
			}
		}
	}

	public function getTotalUsedReceiptMeetings($receiptId){
		$totalUsed = 0;
		$meetings = new Meetings();
		$registrations = $this->getReceiptResgistrations($receiptId);
		while($registration =$registrations->fetch()){
			if($meetings->isStarted($registration['meeting_id'])){
				$totalUsed +=$meetings->getCost($registration['meeting_id']);
			}
		}
		return $totalUsed;
	}

	public function isMeetingFull($meetingId){
		$meetings = new Meetings();
		return ($this->getTotalRegistered($meetingId) >= $meetings->getMaxPlaces($meetingId));
	}

	public function canSubscribeToMeeting($meetingId){
		$meetings = new Meetings();
		$places = $meetings->getMaxSubscriptionPlaces($meetingId);
		$subscriptions = $this->getTotalSubscribed($meetingId);
		if($subscriptions >= $places){
			alert('Les places pour les abonnements pour la séance "'. $meetings->formatMeetingTitleTime($meetingId) .'" sont déjà prises!');
			return false;
		}else{
			return true;
		}
	}

	public function isPeriodSubscriptionFull($periodId){
		$period = DB::getInstance()->select('periods','*','id=' . $periodId)->fetch();
		$actualSubscriptions = DB::getInstance()->select('periods_subscription_receipts','*','period_id=' . $periodId)->rowCount();
		return $period['subscription_places'] == $actualSubscriptions;
	}

	public function canRegisterToMeeting($meetingId){
		$meetings = new Meetings();
		$subscriptionPlaces = $meetings->getMaxSubscriptionPlaces($meetingId);
		$maxPlaces = $meetings->getMaxPlaces($meetingId);
		$registrationAvailable = $maxPlaces - $subscriptionPlaces;

		$registrations = $this->getTotalRegistered($meetingId);
		$subscribed = $this->getTotalSubscribed($meetingId);
		$registrationsTaken = $registrations - $subscribed;
		if($registrationsTaken >= $registrationAvailable){
			$sessions = new Sessions();
			if($sessions->canCardsOverrideSubscriptionsPlaces($meetings->getSessionId($meetingId))){
				return true;
			}else{
				alert('Désolé, les places restantes sont réservés pour les abonnements seulement!');
			}
		}
		return true;
	}

	public function getTotalRegistered($meetingId){
		return $this->getMeetingRegistrations($meetingId)->rowCount();
	}

	public function getTotalSubscribed($meetingId){
		$subscriptionCount = 0;
		$registrations = $this->getMeetingRegistrations($meetingId);
		while($registration = $registrations->fetch()){
			if($registration['weekly']){
				$subscriptionCount++;
			}
		}
		return $subscriptionCount;
	}

	public function isRegistered($memberId, $meetingId){
		if($this::$actualMember != $memberId){
			$this->registeredMeetingOfMember = $this->getRegisteredMeetings($memberId);
		}
		return in_array($meetingId,$this->registeredMeetingOfMember);
	}

	public function getRegisteredMeetings($memberId){
		$this::$actualMember = $memberId;
		$registrations = [];
		$meetings = $this->getMemberRegistredMeetings($memberId);
		while($meeting = $meetings->fetch()){
			$registrations[] = $meeting['meeting_id'];
		}
		return $registrations;
	}

	public function getActualRegistrations($meetingId, $memberId){
		$purchasesModule = new Purchases($memberId);
		$purchases = $purchasesModule->getAll();
		$registrations = [];
		while($purchase = $purchases->fetch()){
			$actualRegistrations = $this->getReceiptMeetingRegistrations($purchase['id'], $meetingId);
			while($registration = $actualRegistrations->fetch()){
				$registration['weekly'] = $purchase['weekly'];
				$registration['member_id'] = $purchase['member_id'];
				$registrations[] = $registration;
			}
		}
		return $registrations;
	}

	public function listActualRegistrations($meetingId, $memberId){
		$registrations = $this->getActualRegistrations($meetingId, $memberId);
		$total = 0;
		$names = '';
		foreach($registrations as $registration){
			$total++;
			if(!$registration['weekly']){
				$names .= $this->formatName($registration) . '<button class="button cancel" onclick=' . "'deleteRegistration(\"" . $registration[ 'id' ] . "\")'" . '>Désinscrire</button><br>';
			}else{
				$names .= $this->formatName($registration) . '<button class="button cancel" onclick=' . "'deleteRegistration(\"" . $registration[ 'id' ] . "\")'" . '>Annuler</button><br>';
			}
		}
		return '('. plurialNoun($total,'inscription'). ')<br>' . $names ;
	}

	public function formatName($registration){
		//TODO vérification pour afficher ou pas le nom??
		return ($registration['waiting'] ? '<span class="expired">En attente: ' : ($registration['weekly'] ? '<span class="">Abonnement: ':'<span class="">Inscrit: ')) . $registration['name'] . '</span>' ;
	}

	public function listPurchases($meetingId, $memberId = null){
		$html = 'Forfait:</br>';
		$html .= "<select id='packageSelector' name='packageSelector'>";
		$options = $this->createPurchasesOptions($meetingId,$memberId);
		if($options === ''){
			$html .= '<button class="button confirm" onclick=' . "'redirect(\"members.php\")'" . '>Acheter un forfait</button>';
		}else{
			$html .= $options . "</select>";
		}
		return $html;
	}

	/**
	 * @param $memberId int
	 * @return string
	 */
	public function createPurchasesOptions($meetingId,$memberId){
		$registrations = new Registrations();
		$meetings = new Meetings();
		$meeting = $meetings->getOne($meetingId)->fetch();
		$purchasesModule = new Purchases($memberId);
		$purchases = $purchasesModule->getAll();
		$packages = new Packages();
		$options = '';
		while( $purchase = $purchases->fetch() ){
			$package = $packages->getOne($purchase[ 'type_id' ])->fetch();
			if ( !$package[ 'weekly' ] ){
				if($purchasesModule->isValid($purchase)){
					$options .= '<option value="' . $purchase[ 'id' ] . '">' . Packages::formatTitle($package) . ' (' . plurialNoun($purchase['meetings_left'], 'séance restante') . ')' . ' (Expire le ' . formatDateTime($purchase[ 'expiration' ]) . ')</option>';
				}
			}else{
				if($meeting['sessionId'] == $purchase['sessionId'] && $purchase['meetings_left'] > 0.5 && !$registrations->isWeekFullForSubscription($meetingId, $purchase[ 'id' ])){
					$options .= '<option value="' . $purchase[ 'id' ] . '">' . Packages::formatTitle($package) . ' (' . plurialNoun($purchase['meetings_left'], 'séance restante') . ') </option>';
				}
			}
		}
		return $options;
	}


	public function listMembersWithPurchases(){
		$html = '<input '. getIdName('memberSelector').' type="hidden" placeholder="id" required>
		<input '. getIdName('memberAC').' type="text" placeholder="Nom du membre" required>
		<script>
			$("#memberAC" ).autocomplete({
				minLength: 0,
				source: function( request, response ) {
					$.ajax({
							url: "php/registrations/listMembersAutocomplete.php",
							dataType: "JSON",
							type: "POST",
							data: {
							q: request.term
						},
						success: function( data ) {
							response( data );
						}
					});
					disableCustomButton();
				},
				select: function(event, ui) {
					enableCustomButton();
					$("#memberSelector").val(ui.item.id);
					$("#memberAC").val(ui.item.firstname + " " + ui.item.lastname);
					changeInfo();
					return false;
				}
			}).autocomplete().data("uiAutocomplete")._renderItem =  function( ul, item ){
				return $( "<li>" )
				.append( "<a>" + item.firstname + " " + item.lastname+ "</a>" )
				.appendTo( ul );
			};
		</script>';
		return $html;
	}

	public function showPlacesLeft($meetingId){
		$meetings = new Meetings();
		$registered = $this->getTotalRegistered($meetingId);
		$max = $meetings->getMaxPlaces($meetingId);
		echo '<span class="'.  ( $registered >= $max ? 'expired' : '' ) .'"> ('. plurialNoun($registered, 'place prise') .' sur '. $max.')</span>';
	}

	public function getPlacesTag($meetingId){
		$meetings = new Meetings();
		$registered = $this->getTotalRegistered($meetingId);
		$max = $meetings->getMaxPlaces($meetingId);
		return '<span class="'.  ( $registered >= $max ? 'expired' : '' ) .'"> ('. $registered .'/'. $max.')</span>';
	}
}