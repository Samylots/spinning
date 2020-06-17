<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Taxes extends Modules
{
	/**
	 * Taxes constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('taxes');
	}

	public function getAll(){
		$queryString = 'select * from (select distinct * from taxes where active = true
						and taxe_type_id in (select id from taxe_types where active = true)
						order by taxe_type_id, date desc, percentage_taxe, id, active)
					  	as taxes group by taxe_type_id';
		return DB::getInstance()->customQuery($queryString);
	}

	public function getOne( $id,$usingActive = true,  $debug = false ){
		$queryString = "SELECT * FROM taxes where id = {$id} AND active = true order by date desc";
		return DB::getInstance()->customQuery($queryString)->fetch();
	}

	public function getTaxe($type){
		$queryString = "SELECT * FROM taxes where taxe_type_id = {$type} AND active = true order by date desc";
		return DB::getInstance()->customQuery($queryString)->fetch();
	}


	public function format( $tax){
		parent::testRequest();
		$taxModule = new TaxTypes();
		$taxType = $taxModule->getOne($tax['taxe_type_id'])->fetch();

		return '<span id="taxeInfo" class="info">
					<span class="code">'. $taxType['title'] .'</span>'.
					($tax["taxe_type_id"] == 3 ? '<span class="value"> ('. $tax['percentage_taxe'] .'$)</span>' :
					'<span class="value"> ('. $tax['percentage_taxe'] .'%)</span>').'
					<span class="value">(depuis le '. formatDateTime($tax['date']) .')</span>
				</span>
				<span id="taxeAction" class="actions">
					<button class="button edit"onclick='. "'editTax(\"". $tax['id'] ."\")'". '>Ajuster</button>'.
					//'<button class="button cancel" onclick='. "'deleteTax(\"". $tax['id'] .'","'. $taxType['id'] ."\")'" .'>Supprimer</button>'.
				'</span>';
	}

	public function adminEditForm( $tax){
		parent::testRequest();

		$taxModule = new TaxTypes();
		$taxType = $taxModule->getOne($tax['taxe_type_id'])->fetch();

		return '<form id="form'. $tax['id'] .'">
					<span id="taxeInfo'. $tax['id'] .'" class="info">
						<span class="title">Taxe: '. $taxType['title']. '</span>'.
						'<input id="typeSelector'. $tax['id'] .'" type="hidden" value="'. $taxType['id'] .'">'.
						($tax["taxe_type_id"] == 3 ? 'Montant :' : 'Pourcentage :').
						'<input id="NewTaxPercentage'. $tax['id'] .'" name="NewTaxPercentage'. $tax['id'] .'" type="number" class="value" value="'. $tax['percentage_taxe'] .'" placeholder="Valeur de la taxe" required>'.
					'</span>
					<span id="taxeAction'. $tax['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditTax(\"" . $tax['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditTax(\"" . $tax['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
					$("#typeSelector'. $tax['id'] .'").val('. $taxType['id'] .');
					$("#form'. $tax['id'] .'").validate({
						rules:{
							NewTaxPercentage'. $tax['id'] .':{ min : 0, max: 100}
						},
						messages:{
							NewTaxPercentage'. $tax['id'] .':{ min : "Le poucentage doit être entre 0% et 100%",
											max: "Le poucentage doit être entre 0% et 100%"}
						}
					});
				</script>';
	}

	public function adminNewForm(){
		$taxModule = new TaxTypes();
		$taxType = $taxModule->getOne(get('type'))->fetch();

		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
						<div class="title">Nom de la taxe: '. $taxType['title']. '</div>'.
						'<input id="typeSelector" name="TaxPercentage" type="hidden" value="'. $taxType['id'] .'">
						Pourcentage:
						<input id="TaxPercentage" name="TaxPercentage" type="number" class="value" value="" placeholder="Pourcentage de la taxe" required>
					</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							TaxPercentage:{ min : 0, max: 100}
						},
						messages:{
							TaxPercentage:{ min : "Le poucentage doit être entre 0% et 100%",
											max: "Le poucentage doit être entre 0% et 100%"}
						}
					});
				</script>';
	}

	public function getTPS($price){
		return $this->roundAmount($this->getTaxPercentage('TPS') * $this->roundAmount($price));
	}

	public function getTVQ($price){
		return $this->roundAmount($this->getTaxPercentage('TVQ') * $this->roundAmount($price));
	}

	public function getMixPrice(){
		return $this->getTaxPercentage('FORFAITS MIXTES')*100;
	}

	private function getTaxPercentage($title){
		$taxesObject = $this->createTaxObject();
		$percentage = 0;
		foreach($taxesObject as $actualTax){
			if($actualTax['title'] == $title){
				$percentage = $actualTax[ 'percentage' ];
			}
		}
		return $percentage;
	}

	private function createTaxObject(){
		$taxes = $this->getAll();
		$taxesObject = [];
		$taxModule = new TaxTypes();
		while($tax = $taxes->fetch()){
			$taxType = $taxModule->getOne($tax['taxe_type_id'])->fetch();
			$taxesObject[] = [
				'title' => $taxType['title'],
				'percentage' => $tax['percentage_taxe'] /100
			];
		}
		return $taxesObject;
	}

	public function roundAmount($amount){
		$amount *= 100;
		$amount = round($amount);
		return ($amount / 100);
	}
}