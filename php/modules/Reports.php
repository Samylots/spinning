<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-19
 * Time: 9:29
 */
class Reports extends Modules
{
	private $registrations;
	private $purchases;
	private $packages;
	private $session;
	private $thingsToSelect = 'meetings, meetings_left, r.active, t.weekly, r.id as receiptId, r.type_id as typeId, price, tps,tvq, purchase_date,paid_date, refund_date,
	refund_value, m.firstname, m.lastname, m.postal_code, m.phone, session_id, expiration';
	private $customTable = 'subscription_receipts as r inner join members as m on r.member_id = m.id inner join subscription_types as t on t.id = r.type_id ';
	/**
	 * Reports constructor.
	 * @param string $table
	 */
	function __construct() {
		$this->session = new Sessions();
		$this->packages = new Packages();
		$this->purchases = new Purchases();
		$this->registrations = new Registrations();
		parent::__construct('subscription_receipts');
		$this->adminToolbar->addOption('Afficher tout', null, 'showAllReports(setContent)');
		$this->adminToolbar->addOption('Cartes actives', null, 'showActiveCardsReports(setContent)');
		$this->adminToolbar->addOption('Abonnements actifs', null, 'showActiveSubscriptions(setContent)');
		$this->adminToolbar->addOption('Cartes entre deux dates', null, 'showBetweenDatesCardsReports(setContent)');
		$this->adminToolbar->addOption('Abonnements entre deux dates', null, 'showBetweenDatesSubscriptonsReports(setContent)');
		$this->adminToolbar->addOption('Achats non payés', null, 'showUnpaidReports(setContent)');
		$this->adminToolbar->addOption('Achats Remboursés', null, 'showRefundsReports(setContent)');
	}

	public function getAll(){
		parent::testRequest();
		try {
			return DB::getInstance()->select($this->customTable, $this->thingsToSelect, 'order by lastname', false);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getActiveCards(){
		parent::testRequest();
		try {
			return DB::getInstance()->select($this->customTable, $this->thingsToSelect, 'weekly is null and refund_date is null and meetings_left > 0', false);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getActiveSubscriptions(){
		parent::testRequest();
		try {
			return DB::getInstance()->select($this->customTable, $this->thingsToSelect, 'weekly is not null and refund_date is null', false);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getCardsBetween($start, $end){
		parent::testRequest();
		try {
			return DB::getInstance()->select($this->customTable, $this->thingsToSelect, 'weekly is null and purchase_date >='.
				DB::getInstance()->getValue($start) . ' AND purchase_date <'. DB::getInstance()->getValue($this->getEndDay($end)), false);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getSubscriptionsBetween($start, $end){
		parent::testRequest();
		try {
			return DB::getInstance()->select($this->customTable, $this->thingsToSelect, 'weekly is not null and purchase_date >='.
				DB::getInstance()->getValue($start) . ' AND purchase_date <'. DB::getInstance()->getValue($this->getEndDay($end)), false);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getRefunds(){
		parent::testRequest();
		try {
			return DB::getInstance()->select($this->customTable, $this->thingsToSelect, 'refund_date is not null', false);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getUnpaid(){
		parent::testRequest();
		try {
			return DB::getInstance()->select($this->customTable, $this->thingsToSelect, 'paid_date is null and refund_date is null', false);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getOne( $id, $usingActive = true, $debug = false ){
		fail();
	}


	public function format( $report){
		parent::testRequest();
		fail();
	}

	public function adminEditForm( $report){
		parent::testRequest();
		fail();
	}

	public function adminNewForm(){
		parent::testRequest();
		fail();
	}

	public function startTable(){
		$html = Helper::tip('Les dates sont du format "mm-jj-aaaa"!').
			'<table id="adminReportTable" class="table table-condensed table-striped table-hover">';
		return $html . $this->getTablerHeader();
	}

	private function getTablerHeader(){
		return'<thead> <tr>
		            <th> Nom </th>
		            <th> Prénom </th>
		            <th> Tel. </th>
		            <th> Total </th>
		            <th> TPS </th>
		            <th> TVQ </th>
		            <th> Forfait </th>
		            <th> Session </th>
		            <th> Séances </th>
		            <th> Achat </th>
		            <th> Paiement </th>
		            <th> Remboursé </th>
		            <th> Montant </th>
		        </tr></thead> <tbody>';
	}
//<th> Code Postal </th>
/*<td>
                    '. formatPostalCode($row['postal_code']) .'
                </td> */
	public function formatRow($row){
		$session = $this->session->getOne($row['session_id'])->fetch();
		$package = $this->packages->getOne($row['typeId'])->fetch();
		return '<tr class="'. ($row['meetings_left'] > 0 ? '' : 'expired').'"> <td>
                    '. $row['lastname'] .'
                </td> <td>
                    '. $row['firstname'] .'
                </td> <td>
                    '. formatPhone($row['phone']) .'
                </td> <td>
                    '. formatPrice(floatval($row['price']) + floatval($row['tps']) + floatval($row['tvq']), 2) .'
                </td> <td>
                    '. formatPrice($row['tps'],2) .'
                </td> <td>
                    '. formatPrice($row['tvq'],2) .'
                </td> <td>
                    '. $this->packages->formatTitle($package) .'
                </td> <td>
                    '. $this->session->formatTitle($session, false) .'
                </td> <td>
                    '. $row['meetings_left'] .'
                </td> <td>
                    '. $this->formatDate($row['purchase_date']) .'
                </td> <td>
                    '. $this->formatDate($row['paid_date']) .'
                </td> <td>
                    '. $this->formatDate($row['refund_date']) .'
                </td> <td>
                    '.  formatPrice($row['refund_value'],2) .'
                </td> </tr>';
	}

	public function formatActiveRow($row){
		$session = $this->session->getOne($row['session_id'])->fetch();
		$purchase = $this->purchases->getOne($row['receiptId'])->fetch();
		if(!$this->purchases->isValid($purchase)){
			return '';
		}
		$package = $this->packages->getOne($row['typeId'])->fetch();
		return '<tr> <td>
                    '. $row['firstname'] .'
                </td> <td>
                    '. $row['lastname'] .'
                </td> <td>
                    '. formatPhone($row['phone']) .'
                </td> <td>
                    '. formatPrice(floatval($row['price']) + floatval($row['tps']) + floatval($row['tvq']), 2) .'
                </td> <td>
                    '. formatPrice($row['tps'],2) .'
                </td> <td>
                    '. formatPrice($row['tvq'],2) .'
                </td> <td>
                    '. $this->packages->formatTitle($package) .'
                </td> <td>
                    '. $this->session->formatTitle($session, false) .'
                </td> <td>
                    '. $row['meetings_left'] .'
                </td> <td>
                    '. $this->formatDate($row['purchase_date']) .'
                </td> <td>
                    '. $this->formatDate($row['paid_date']) .'
                </td> <td>
                    '. $this->formatDate($row['refund_date']) .'
                </td> <td>
                    '.  formatPrice($row['refund_value'],2) .'
                </td> </tr>';
	}

	public function endTable(){
		return '</tbody></table>'.
			'<script>
		        $("#adminReportTable").dataTable({
					    language: {
					        processing:     "Traitement en cours...",
					        search:         "Rechercher&nbsp;:",
					        lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
					        info:           "Affichage de l\'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
					        infoEmpty:      "Affichage de l\'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
					        infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
					        infoPostFix:    "",
					        loadingRecords: "Chargement en cours...",
					        zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
					        emptyTable:     "Aucune donnée &agrave; afficher",
					        paginate: {
					            first:      "Premier",
					            previous:   "Pr&eacute;c&eacute;dent",
					            next:       "Suivant",
					            last:       "Dernier"
					        },
					        aria: {
					            sortAscending:  ": activer pour trier la colonne par ordre croissant",
					            sortDescending: ": activer pour trier la colonne par ordre décroissant"
					        }
					    }
					} );
			</script>';
	}

	private function formatDate($string){
		if($string == ''){
			return '---';
		}
		$date = new DateTime($string);
		return $date->format('m-d-Y');
	}

	private function getEndDay( $date){
		return date('Y-m-d', (strtotime(str_replace('-', '/', $date))+DAY_STAMP));
	}

}