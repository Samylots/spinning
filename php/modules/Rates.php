<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Rates extends Modules
{
	private $actualId;
	/**
	 * Schedules constructor.
	 * @param string $table
	 */
	function __construct( $sessionId) {
		$this->actualId = $sessionId;
		parent::__construct('sessions_subscription_types');
		$sessionModule = new Sessions();
		$actual_session = $sessionModule->getOne($sessionId)->fetch();

		$this->adminToolbar->addOption('Déterminer le prix d\'un forfait pour la session "'. $actual_session['title'] .'"', null, 'newRate()');
	}

	public function getAll(){
		$queryString = 'SELECT * FROM '. $this->table . ' left join subscription_types on subscription_type_id = id WHERE session_id = '.
			$this->actualId . ' AND subscription_types.active = true AND ' . $this->table . '.active = true';
		return DB::getInstance()->customQuery($queryString);
	}

	public function add( $data ){
		$result = $this->edit($data['subscription_type_id'],$data);
		if($result < 1){
			parent::add($data);
		}
	}

	public function delete( $id ){
		$queryString = 'UPDATE ' . $this->table. ' SET active=false WHERE session_id = ' . $this->actualId. ' AND subscription_type_id='. $id;
		return (DB::getInstance()->customQuery($queryString) == 1);
	}

	public function edit( $id, $data, $debug = false ){
		try {
			$queryString = DB::getInstance()->getUpdateBaseString($this->table, $data);
			$queryString .= ' WHERE ' . 'session_id = ' . $this->actualId. ' AND subscription_type_id='. $id;
			if($debug){
				var_dump($queryString);
			}
			return DB::getInstance()->customQuery($queryString)->rowCount();
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getOne( $id, $usingActive = true,  $debug = false ){
		try {
			return DB::getInstance()->select($this->table, '*', 'subscription_type_id='. $id . ' AND session_id = ' . $this->actualId);
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function format( $rate){
		parent::testRequest();

		$taxModule = new Taxes();
		$packageModule = new Packages();
		$actualPackage = $packageModule->getOne($rate['subscription_type_id'])->fetch();

		return '<span id="=rateInfo" class="info">
					<span class="title">'. $packageModule->formatTitle($actualPackage) .'</span>
					<span class="total">À '. formatPrice($rate['rate'],2) .'</span>
					<span class="taxes">( +TX = '. formatPrice($rate['rate'] + $taxModule->getTVQ($rate['rate']) + $taxModule->getTPS($rate['rate']),2) .' )</span>'.
				'</span>'
				.'</span>
				<span id="rateAction" class="actions">
					<button class="button edit"onclick='. "'editRate(\"". $rate['subscription_type_id'] ."\")'". '>Modifier</button>
					<button class="button cancel" onclick='. "'deleteRate(\"". $rate['subscription_type_id'] ."\")'" .'>Supprimer</button>
				</span>';
	}


	public function adminEditForm( $rate){
		$packageModule = new Packages();
		parent::testRequest();
		return '<form id="form'. $rate['subscription_type_id'] .'">
			<span id="scheduleInfo'. $rate['subscription_type_id'] .'" class="info">
				Forfait: '.
				$packageModule->formatTitle($packageModule->getOne($rate['subscription_type_id'])->fetch())
				.'</br>Prix du forfait pour cette session:
				<input id="newPrice'. $rate['subscription_type_id'] .'" name="NewPrice'. $rate['subscription_type_id'] .'" type="number" placeholder="Prix pour cette session" required value="'. $rate['rate'] .'">
			</span>'.
			'<span id="scheduleAction'. $rate['subscription_type_id'] .'" class="actions">'.
				'<button class="button confirm" onclick=' . "'saveEditRate(\"" . $rate['subscription_type_id'] . "\")'" . '>Enregistrer</button>' .
				'<button class="button cancel" onclick='. "'cancelEditRate(\"" . $rate['subscription_type_id'] . "\")'" .'>Annuler</button>'.
			'</span>'.
		'</form>
			<script>
			$("#form'. $rate['subscription_type_id'] .'").validate({
				rules:{
					NewPrice'. $rate['subscription_type_id'] .':{ min : 0}
						},
				messages:{
					NewPrice'. $rate['subscription_type_id'] .':{ min : "Le prix ne peut être négatif"}
				}
				});
			</script>';
	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">'.
						$this->listAvailablePackages()
						.'Prix du forfait pour cette session:
						<input id="Price" name="Price" type="number" placeholder="Prix pour cette session" required>
					</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							Price:{ min : 0}
						},
						messages:{
							Price:{ min : "Le prix ne peut être négatif"}
						}
					});
				</script>';
	}

	public function listAvailablePackages(){
		$queryString = 'SELECT * FROM subscription_types where id not
						in (SELECT subscription_type_id FROM sessions_subscription_types
						where session_id = '. $this->actualId .' and active = true);';

		$packageModule = new Packages();
		$availablesPackages = DB::getInstance()->customQuery($queryString);

		$html = "Forfait:<select id='packageSelector' name='packageSelector'>";
		$options = "";
		while($package = $availablesPackages->fetch()){
			$options .= '<option value="'. $package['id'] .'">'. $packageModule->formatTitle($package) . '</option>';
		}
		$html .= $options . "</select>";
		if(!empty($options)){
			return $html;
		}else{
			alert('Tous les forfaits ont un prix de défini pour cette session!');
			return null;
		}
	}

	public function getPrice($packageId){
		return $this->getOne($packageId)->fetch()['rate'];
	}


}