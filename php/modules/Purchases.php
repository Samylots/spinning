<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Purchases extends Modules
{
	private $fields = 'r.id as id, type_id, meetings, meetings_left,price,tps,tvq,purchase_date,paid_date,expiration,r.cancellation_deadline as
					receiptCancelDeadline, type.cancellation_deadline as meetingCancelDeadline, refund_date, refund_value,
					member_id, r.active, registration_deadline,limit_registration_advance, weekly, discount_id as discountId, session_id as sessionId';
	private $customTable = 'subscription_receipts as r LEFT JOIN subscription_types as type on r.type_id = type.id';
	private $memberId;
	private $lastUsed;
	private $user;
	/**
	 * Schedules constructor.
	 * @param string $table
	 */
	function __construct($memberId = null) {
		$this->user = new User();
		$this->memberId =  $memberId ? $memberId : $this->user->getUserId() ;
		parent::__construct('subscription_receipts');
		$this->toolbar->addOption('Acheter une carte',null,'buyCard('. $memberId .')', !hasPosted('memberId'));
		$this->toolbar->addOption('Acheter une carte pour le membre',null,'buyCard('. $memberId .')', hasPosted('memberId'));
		$this->toolbar->addOption('Acheter un abonnement pour le membre',null,'buySubscription('. $memberId .')', hasPosted('memberId'));
		$this->toolbar->addOption('Acheter un abonnement',null,'buySubscription('. $memberId .')', $this->user->isAdmin() && !hasPosted('memberId'));
	}

	public function setMemberId($memberId){
		$this->memberId = $memberId;
	}

	public function getAll(){
		return DB::getInstance()->select($this->customTable, $this->fields, 'member_id=' . $this->memberId .
			' and r.active=true order by expiration, meetings_left', false);
	}

	public function getAllOlder($expiration){
		$expiration = new DateTime($expiration);
		$expiration = date('Y-m-d', $expiration->getTimestamp()+DAY_STAMP);
		return DB::getInstance()->select($this->customTable, $this->fields, 'member_id=' . $this->memberId .
			' and expiration < '. DB::getInstance()->getValue($expiration) .' order by expiration desc', false);
	}

	public function getOne( $id, $usingActive = true, $debug = false ){
		//Need to save the member that have this receipt, because the next call need to be about this user normally...
		$this->memberId = DB::getInstance()->select($this->customTable, $this->fields, 'r.id=' . $id, false)->fetch()['member_id'];
		return DB::getInstance()->select($this->customTable, $this->fields, 'r.id=' . $id, false);
	}

	public function add( $data ){
		$data['member_id'] = $this->memberId;
		return parent::add($data);
	}

	public function format( $purchase){
		parent::testRequest();
		$packageModule = new Packages();
		$actualPackage = $packageModule->getOne($purchase['type_id'])->fetch();
		$currentDate = date('Y-m-d');
		$startDate = date('Y-m-d', strtotime($purchase['expiration']));
		$html = '';
		if(!$this->isValid($purchase) || $currentDate > $startDate){
			$html .= '<span id="purchaseInfo" class="info expired">';
		}else{
			$html .= '<span id="purchaseInfo" class="info">';
		}
		if($purchase['weekly']){
			$sessions = new Sessions();
			$session = $sessions->getOne($purchase['sessionId'])->fetch();
			$credits = DB::getInstance()->select('credits','*','subscription_receipt_id = ' . $purchase['id'] )->fetch();
			if($credits){
				$totalCredits = intval($credits['total_credits']);
			}else{
				$totalCredits = 0;
			}
			$html .= '<span class="title">'. $packageModule->formatTitle($actualPackage) . ' (' . plurialNoun($purchase['meetings_left'], 'séance restante') . ')<br>
			(' . plurialNoun($totalCredits, 'crédit') . ')'.' (Session: ' . $sessions->formatTitle($session) . ')'.'</span>';
		}else{
			$html .= '<span class="title">'. $packageModule->formatTitle($actualPackage) . ' (' . plurialNoun($purchase['meetings_left'], 'séance restante') . ')' .'</span>';
		}
		$html .= '<span class="title">Acheté le '. formatDateTime($purchase[ 'purchase_date' ]) .'</span>
		<span class="title">Payé le '. formatDateTime($purchase[ 'paid_date' ]) .'</span>
		<span class="total">('. formatPrice($purchase['price'],2) .'</span>
		<span class="taxes"> +TX = '. formatPrice($purchase['price'] + $purchase['tps'] + $purchase['tvq'],2) .' )</span>';
		if($this->isValid($purchase) && $currentDate < $startDate){
			$html .= '<span class="title">Expire le ' . formatDateTime($purchase[ 'expiration' ]) . '</span>';
			if($purchase['weekly']){
				$registrations = new Registrations();
				$schedules = new Schedules($purchase['sessionId']);
				$periodIds = $registrations->getMemberSubscribedPeriodsWithReceipt($this->memberId, $purchase['id']);
				if($periodIds->rowCount() >1){
					$html .= '<span class="periods">Périodes abonnés: <br>';
					while($periodId = $periodIds->fetch()){
						$html .= ' -'. $schedules->formatPeriodTitle($periodId['period_id']) . '<br>';
					}
				}else{
					$html .= '<span class="periods">Période abonné: ';
					$html .= ' '. $schedules->formatPeriodTitle($periodIds->fetch()['period_id']);
				}
				$html .= '</span>';
			}
		}else if($currentDate == $startDate){
			$html .= '<span class="title">Expire AUJOURD\'HUI!</span>';
		}else{
			$html .= '<span class="title">EXPIRÉ!</span>';
		}

		if($purchase[ 'refund_date' ]){
			$html .= '<span class="title">Remboursemment de '. formatPrice($purchase['refund_value'],2) .' le ' . formatDateTime($purchase[ 'refund_date' ]) . '</span>';
		}

		$html .='</span>'
		.'</span>
		<span id="purchaseAction" class="actions">';

		if($this->user->isAdmin()){
			if(!$purchase[ 'paid_date' ]){
				$html .= '<button class="button edit"onclick=' . "'editPurchasePrice(\"" . $purchase[ 'id' ] . "\")'" . '>Modifier prix</button>';
				$html .= '<button class="button confirm"onclick=' . "'payPurchase(\"" . $purchase[ 'id' ] . "\")'" . '>Payer</button>';
			}else if(!$purchase[ 'refund_date' ]){
				$html .= '<button class="button cancel"onclick=' . "'refundPurchase(\"" . $purchase[ 'id' ] . "\")'" . '>Rembourser</button>';
			}
			//$html .= '<button class="button cancel"onclick=' . "'deletePurchase(\"" . $purchase[ 'id' ] . "\")'" . '>Supprimer</button>';
		}

		$html .= '</span>';
		return $html;
	}

	public function editPriceForm($purchaseId){
		$purchase = $this->getOne($purchaseId)->fetch();
		$html = '<form id="form'. $purchase['id'] .'">';
		$html .= 'Prix à payer avant taxes:';
		$html .= '<input id="newPricePurchase" name="newPricePurchase" type="number" value="'. $purchase['price'] .'" placeholder="Nouveau prix avant taxes" required>';
		$html .= '</form>';
		$html .= '<script>
					$("#form'. $purchase['id'] .'").validate({
						rules:{
							newPricePurchase:{
							required: true}
						},
						messages:{
							newPricePurchase:{
							required: "Ce champ est obligatoire!"}
						}
					});
				  </script>';
		return $html;
	}

	public function refundPurchaseForm($purchaseId){
		$purchase = $this->getOne($purchaseId)->fetch();
		$html = '<form id="form'. $purchase['id'] .'">';
		$priceToRefund = $this->calculateHowMuchToRefund($purchase);
		$html .= 'Montant (estimé) à rembourser:';
		$html .= '<input id="newRefundValuePurchase" name="newRefundValuePurchase" type="number" value="'. $priceToRefund .'" placeholder="Montant du remboursement" required>';
		$html .= '</form>';
		$html .= '<script>
					$("#form'. $purchase['id'] .'").validate({
						rules:{
							newRefundValuePurchase:{
							required: true}
						},
						messages:{
							newRefundValuePurchase:{
							required: "Ce champ est obligatoire!"}
						}
					});
				  </script>';
		return $html;
	}

	private function calculateHowMuchToRefund($purchase){
		$registrations = new Registrations();
		$totalPaid = floatval($purchase['price']) + floatval($purchase['tps'])+ floatval($purchase['tvq']);
		if($purchase['weekly']){
			$sessions = new Sessions();
			$session = $sessions->getOne($purchase['sessionId'])->fetch();
			$purchaseDate = new DateTime($purchase['purchase_date']);
			$sessionStartDate = new DateTime($session['start_date']);
			if($purchaseDate->getTimestamp() > $sessionStartDate->getTimestamp()){
				$startDate = $purchaseDate;
			}else{
				$startDate = $sessionStartDate;
			}
			$sessionEndDate = new DateTime($session['end_date']);
			$purchaseTenPercentDate = new DateTime($purchase['receiptCancelDeadline']);
			if($purchaseTenPercentDate->getTimestamp() < time()){
				echo Helper::tip('Attention, la règle des 10% de protection du consommateur n\'est plus respectée!');
			}
			$sessionLength = $sessionEndDate->getTimestamp() - $startDate->getTimestamp();
			$leftTime = $sessionEndDate->getTimestamp() - time();
			$percentLeft = $leftTime / $sessionLength;
		}else{
			$meetingsUsed = $registrations->getTotalUsedReceiptMeetings($purchase['id']);
			$tenPercentMeetings = ceil(floatval($purchase['meetings']) * 0.1);
			if($tenPercentMeetings <= $meetingsUsed){
				echo Helper::tip('Attention, la règle des 10% de protection du consommateur n\'est plus respectée!');
			}
			$totalPaid = floatval($purchase['price']) + floatval($purchase['tps'])+ floatval($purchase['tvq']);
			$percentLeft = (floatval($purchase['meetings'])-$meetingsUsed) / floatval($purchase['meetings']);
		}
		$estimatedValue = ceil(($percentLeft * $totalPaid)*100)/100;

		return ($estimatedValue > $totalPaid ? ceil($totalPaid*100)/100 : $estimatedValue);
	}

	public function refundPurchase($purchaseId, $refundValue){
		$purchase = $this->getOne($purchaseId)->fetch();
		//TODO si c'est un subscription, periods subscription to false et meetings aussi
		if($purchase['weekly']){
			$this->edit($purchaseId, [
				'refund_date' => date('Y-m-d', time()),
				'refund_value' => $refundValue,
				//'active' => '0'
			]);
		}else{
			$this->edit($purchaseId, [
				'refund_date' => date('Y-m-d', time()),
				'refund_value' => $refundValue,
				//'active' => '0'
			]);
		}
	}

	public function editPrice($purchaseId, $newPrice){
		$taxes = new Taxes();
		$this->edit($purchaseId,[
			'price' => $newPrice,
			'tps' => $taxes->getTPS($newPrice),
			'tvq' => $taxes->getTVQ($newPrice)
		]);
	}

	public function adminEditForm( $purchase){
		parent::testRequest();
		fail('CANT EDIT PURCHASES');
	}

	public function adminNewForm(){
		parent::testRequest();
		fail('USE OTHER FUNCTION TO BUY CARD OR SUBSCRIPTION');
	}

	public function cardForm(){
		$html = $this->createSessionList();
		$html .= $this->listCards();
		$html .= $this->createDiscountInput();
		$html .= $this->createSubs();
		return $html;
	}

	private function listCards($sessionId = null){
		$sessions = new Sessions();
		$sessionId = $sessionId ? $sessionId : $sessions->getAvailableSessions()[0]['id'];
		$html = 'Types de carte:</br>';
		$html .= $this->createPackageList($this->getCards($sessionId));
		return $html;
	}

	private function createPackageList($packages){
		$html = "<select id='packageSelector'>";
		$html .= $this->createPackageOptions($packages);
		return $html . "</select>";
	}

	public function createPackageOptions($packages){
		$html = '';
		foreach( $packages as $package ){
			$html .= '<option value="' . $package[ 'id' ] . '">' . Packages::formatTitle($package) . '</option>';
		}
		if($html === ''){
			$html .= '<option disabled> Aucun forfait disponible... </option>';
		}
		return $html;
	}

	private function createSessionList(){
		$sessions = new Sessions();
		$availableSessions = $sessions->getAvailableSessions();
		$html = 'Session:';
		$html .= "<select id='sessionSelector'>";
		foreach( $availableSessions as $session ){
			$html .= '<option value="'. $session['id'] .'">'. Sessions::formatTitle($session) . '</option>';
		}
		return $html . "</select>";
	}

	private function createDiscountInput(){
		return 'Code rabais: <input id="discountCode" name="discountCode" type="text" placeholder="Code rabais">
				<button class="button edit" onclick="applyDiscount()">Appliquer</button>';
	}

	private function createSubs(){
		return '<div>
					<label>Sous-total: </label> <span id="subTot"></span></br>
					<label>Rabais: </label> <span id="discount"></span></br>
					<label>Taxes: </label> <span id="taxes"></span></br>
					<span id="subSeparator"> </span></br>
					<label>Total: </label> <span id="total"></span></br>
				</div>';
	}

	public function buyCard($sessionId, $cardTypeId, $discountCode){
		$discounts = new Discounts();
		$types = new Packages();
		$meetings = $types->getMeetingsAllowed($cardTypeId);
		$total = $this->getCardSubtotal($sessionId,$cardTypeId,$discountCode);

		$taxes = new Taxes();
		$this->add([
			'meetings' => $meetings,
			'meetings_left' => $meetings,
			'price' => ($total >= 0 ? $total : 0),
			'tps' => $taxes->getTPS($total),
			'tvq' => $taxes->getTVQ($total),
			'purchase_date' => date('Y-m-d', time()),
			'expiration' => date('Y-m-d', (time() + YEAR_STAMP*2)),
			'cancellation_deadline' => date('Y-m-d', $this->getTenPercentTime($sessionId)),
			'type_id' => $cardTypeId,
			'session_id' => $sessionId,
			'discount_id' => $discounts->getDiscountIdByCode($discountCode)
		]);
	}

	public function getCardSubtotal($sessionId, $cardTypeId){
		$rates = new Rates($sessionId);
		$cardTypeRate = $rates->getOne($cardTypeId)->fetch();
		return $cardTypeRate['rate'];
	}

	public function processDiscount($subTotal, $discountCode){
		$discounts = new Discounts();
		$discountValue =  $discounts->getDiscountValue($discountCode,$subTotal);
		return $subTotal - $discountValue;
	}


	public function subscriptionForm(){
		$html = $this->createSessionList();
		$html .= $this->listSubscriptions();
		$html .= $this->createSessionSchedule();
		$html .= $this->createDiscountInput();
		$html .= $this->createSubs();
		return $html;
	}

	private function listSubscriptions($sessionId = null){
		$sessions = new Sessions();
		$sessionId = $sessionId ? $sessionId : $sessions->getAvailableSessions()[0]['id'];
		$html = 'Types d\'abonnement:</br>';
		$html .= $this->createPackageList($this->getSubscriptions($sessionId));
		return $html;
	}

	private function createSessionSchedule($sessionId = null){
		$html =  '<div id="scheduleSubscription">';
		if($sessionId){
			$this->getSessionSchedule($sessionId);
		}
		$html .= '</div>';
		return $html;
	}

	public function getSessionSchedule($sessionId){
		$scheduleModule = new Schedules($sessionId);
		$schedule = $scheduleModule->memberFormat($scheduleModule->getAll());
		$html = '<div class="scheduleTable">';
		if($sessionId){
			$html .= $schedule;
		}
		$html .= '</div>';
		return $html;
	}

	public function buySubscription( $sessionId, $subscriptionTypeId, $discountCode, $selectedPeriods){
		//TODO Faire du prorata pour si en plein millieu de session?
		//TODO faire marcher l'asignassion de rabais à des forfaits
		$discounts = new Discounts();
		$sessions = new Sessions();
		$taxes = new Taxes();
		$session = $sessions->getOne($sessionId)->fetch();
		$types = new Packages();
		$meetingsAllowed = $types->getMeetingsAllowed($subscriptionTypeId);
		if( $meetingsAllowed != count($selectedPeriods)){
			alert('Vous n\'avez pas choisi toutes les périodes pour combler le forfait choisi!');
		}

		$this->checkIfCanSubscribeToAll($selectedPeriods);
		$total = $this->getSubscriptionSubtotal($sessionId,$subscriptionTypeId,$selectedPeriods);
		$this->add([
			'meetings' => $meetingsAllowed,
			'meetings_left' => '0', //will gain only by credits!
			'price' => ($total >= 0 ? $total : 0),
			'tps' => $taxes->getTPS($total),
			'tvq' => $taxes->getTVQ($total),
			'purchase_date' => date('Y-m-d', time()),
			'expiration' => date('Y-m-d', strtotime($session['end_date'])),
			'cancellation_deadline' => date('Y-m-d', $this->getTenPercentTime($sessionId)),
			'type_id' => $subscriptionTypeId,
			'session_id' => $sessionId,
			'discount_id' => $discounts->getDiscountIdByCode($discountCode)
		]);
		$receiptId = DB::getInstance()->getLast();
		$this->subscribe($receiptId, $selectedPeriods);
	}

	private function checkIfCanSubscribeToAll($selectedPeriods){
		$meetings = new Meetings();
		$registrations = new Registrations();
		foreach($selectedPeriods as $periodId){
			$periodMeetings = $meetings->getPeriodMeetings($periodId);
			while($meeting = $periodMeetings->fetch()){
				$registrations->canSubscribeToMeeting($meeting['meetingId']);
			}
		}
	}

	private function subscribe($receiptId, $selectedPeriods){
		$members = new Members();
		$meetings = new Meetings();
		$member = $members->getOne($this->memberId)->fetch();
		$registrations = new Registrations();
		foreach($selectedPeriods as $periodId){
			DB::getInstance()->add('periods_subscription_receipts',[
				'period_id' => $periodId,
				'subscription_receipt_id' => $receiptId,
				'active' => true
			]);
			$periodMeetings = $meetings->getPeriodMeetings($periodId);
			while($meeting = $periodMeetings->fetch()){
				if(!$meetings->isStarted($meeting['meetingId'])){
					if(!$meetings->isRegrationLimitPassed($meeting['meetingId'],$receiptId)){
						if($registrations->canSubscribeToMeeting($meeting['meetingId'])){
							$registrations->add([
								'subscription_receipt_id' => $receiptId,
								'meeting_id' => $meeting[ 'meetingId' ],
								'name' => $member[ 'nickname' ],//$members->getFullName($member),
								'registred' => now()//,
								//'waiting' => $registrations->isMeetingFull($meeting['meetingId'])
							]);
						}
					}
				}
			}
		}
	}

	public function getSubscriptionSubtotal($sessionId, $subscriptionTypeId, $selectedPeriods){
		$subTotal =0;
		foreach( $selectedPeriods as $periodId ){
			$subTotal += $this->getSubscriptionPeriodSubtotal($sessionId,$subscriptionTypeId,$periodId);
		}
		return $subTotal;
	}

	public function getSubscriptionPeriodSubtotal($sessionId, $subscriptionTypeId, $periodId){
		//(prix forfait / nombre de meetings par semaine + surplus mixtes si c'est mixte) * ratio restant
		$rates = new Rates($sessionId);
		$packages = new Packages();
		$meetings = new Meetings();
		$meetingPerWeeks = $packages->getMeetingsAllowed($subscriptionTypeId);
		$price = $rates->getPrice($subscriptionTypeId);
		if($meetings->isPeriodMix($periodId)){
			$taxes = new Taxes();
			$mixPrice = $taxes->getMixPrice();
		}else{
			$mixPrice = 0;
		}
		return (($price / $meetingPerWeeks) + $mixPrice) * $meetings->getPeriodMeetingsLeftRatio($periodId);
	}

	private function getTenPercentTime($sessionId){
		$sessions = new Sessions();
		$session = $sessions->getOne($sessionId)->fetch();
		$now = time();
		$endTime = strtotime($session['end_date']);
		$diff = $endTime - $now;
		return $now + intval(($diff/100)*10);
	}

	/**
	 * Find all cards types
	 * @return array
	 */
	public function getCards($sessionId){
		if($sessionId){
			$cards = [ ];
			$packages = new Packages();
			$rates = new Rates($sessionId);
			$availableRates = $rates->getAll();
			while( $availableRate = $availableRates->fetch() ){
				$package = $packages->getOne($availableRate[ 'subscription_type_id' ])->fetch();
				if ( !$package[ 'weekly' ] ){
					$cards[] = $package;
				}
			}
			return $cards;
		}else{
			return [];
		}
	}

	/**
	 * Find all subscriptions types
	 * @return array
	 */
	public function getSubscriptions($sessionId){
		if($sessionId){
			$subscriptions = [ ];
			$packages = new Packages();
			$rates = new Rates($sessionId);
			$availableRates = $rates->getAll();
			while( $availableRate = $availableRates->fetch() ){
				$package = $packages->getOne($availableRate[ 'subscription_type_id' ])->fetch();
				if ( $package[ 'weekly' ] ){
					$subscriptions[] = $package;
				}
			}
			return $subscriptions;
		}else{
			return [];
		}
	}

	public function canSelectPeriod($packageId, $selectedPeriodsArray){
		$packages = new Packages();
		$package = $packages->getOne($packageId)->fetch();
		if($package['meetings_allowed'] < count($selectedPeriodsArray)){
			alert('Veuillez choisir un abonnement avec plus de périodes afin de pouvoir sélectionner cette période!');
		}
	}

	public function hasPurchases(){
		return $this->getAll()->rowCount() > 0;
	}

	public function isValid( $purchase){
		//vérifier que cela valide bien s'il est valid ou pas quand ya un refund....
		$sessions = new Sessions();
		$now = time();
		$session = $sessions->getOne($purchase['sessionId'])->fetch();
		$sessionEndDate = new DateTime($session['end_date']);
		$expirationDate = new DateTime($purchase['expiration']);
		if(!$purchase['active'] || $purchase['refund_date']){
			return false;
		}
		if(!$purchase['weekly']){
			return ( $purchase[ 'meetings_left' ] > 0 ) && ( $expirationDate->getTimestamp() >= $now );
		}else{
			return ($sessionEndDate->getTimestamp() >= $now);
		}
	}

	public function hasEnough($purchase, $cost){
		$available = floatval($purchase[ 'meetings_left' ]);
		$tempAvailable = $available;
		if( $cost <= $available){
			return true;
		}else{
			$purchases = $this->getAll();
			while( $tempAvailable < $cost && $purchaseTemp = $purchases->fetch()){
				if($purchaseTemp['id'] != $purchase['id'] && $this->isValid($purchaseTemp)){
					$tempAvailable += floatval($purchaseTemp[ 'meetings_left' ]);
				}
			}
			if($cost <= $tempAvailable){
				return true;
			}else{
				alert('Désolé, mais le forfait n\'a plus assez de séances disponibles pour s\'inscrire... (' . $cost .
					' requis, ' . plurialNoun($available, 'disponible') . ') <br> Il est donc impossible de vous inscrire!');
			}
		}
		return null;
	}

	public function markAsPaid($purchaseId){
		if($this->user->isAdmin()){
			$this->edit($purchaseId,[
				'paid_date' => date('Y-m-d', time())
			]);
		}else{
			fail('NOT AN ADMIN!');
		}
	}

	/**
	 * @param $purchaseId int
	 * @param $cost float
	 */
	public function pay($purchaseId, $cost){
		$purchase = $this->getOne($purchaseId)->fetch();
		$available = floatval($purchase['meetings_left']);
		if($cost <= $available){
			$left = ( floatval($purchase[ 'meetings_left' ]) - $cost );
			$left = ($left === floatval('0') ? '0' : $left);
			$this->edit($purchaseId, [
				'meetings_left' => $left
			]);
			$this->lastUsed = $purchaseId;
		}else{
			$this->edit($purchaseId, [
				'meetings_left' => '0'
			]);
			$cost -= $available;
			$purchases = $this->getAll();
			while($cost >0 && $purchase = $purchases->fetch()){
				if($purchase['id'] != $purchaseId && $this->isValid($purchase)){
					$available = floatval($purchase[ 'meetings_left' ]);
					if($cost <= $available){
						$this->edit($purchase['id'], [
							'meetings_left' => ( $available - $cost )
						]);
					}else{
						$this->edit($purchase['id'], [
							'meetings_left' => '0'
						]);
					}
					$this->lastUsed = $purchase['id'];
					$cost -= $available;
				}
			}
		}
	}

	/**
	 * @return int : Only the last card that paid it...
	 */
	public function getLastUsed(){
		return $this->lastUsed;
	}

	/**
	 * @param $purchaseId int
	 * @param $cost float
	 */
	public function gain($purchaseId, $cost){
		$purchase = $this->getOne($purchaseId)->fetch();
		if((floatval($purchase['meetings_left']) + $cost) <= floatval($purchase['meetings'])){
			$this->edit($purchaseId, [
				'meetings_left' => ( floatval($purchase[ 'meetings_left' ]) + $cost )
			], true);
		}else{
			$this->edit($purchaseId, [
				'meetings_left' => floatval($purchase[ 'meetings' ]) //full
			], true);
			$gained = floatval($purchase['meetings']) - floatval($purchase[ 'meetings_left' ]);
			$cost -= $gained;
			$olderPurchases = $this->getAllOlder($purchase['expiration']);
			while($cost >0 && $purchase = $olderPurchases->fetch()){
				if((floatval($purchase['meetings_left']) + $cost) <= floatval($purchase['meetings'])){
					$this->edit($purchase['id'], [
						'meetings_left' => ( floatval($purchase[ 'meetings_left' ]) + $cost )
					], true);
					$cost=0;
				}else{
					$this->edit($purchase['id'], [
						'meetings_left' => floatval($purchase[ 'meetings' ]) //full
					], true);
					$gained = floatval($purchase[ 'meetings' ]) - floatval($purchase[ 'meetings_left' ]);
					$cost -= $gained;
				}

			}
		}
	}
}