<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Discounts extends Modules
{
	/**
	 * Discounts constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('discounts');
		$this->adminToolbar->addOption('Nouveau rabais',null,'newDiscount()');
	}

	/**
	 * @param $discount array
	 * @return string
	 */
	public function format( $discount){
		parent::testRequest();

		return '<span id="discountInfo" class="info '. ($this->isExpired($discount)? 'expired' : '') .'">
			<span class="total">Rabais: '.
		$discount['value'] . $discount['type']
		.'</span>
		<span class="title">Code: '.
		$discount['code']
		.'</span>
		<span class="start">Du '.
		formatDateTime($discount['start'])
		.'</span>
		<span class="end"> au '.
			formatDateTime($discount['end'])
		.'
		</span>'.
		$this->getMinAge($discount).'</br>'.
		$this->getHolder($discount)

		.'</span>
		<span id="discountAction" class="actions">
			<button class="button edit"onclick='. "'editDiscount(\"". $discount['id'] ."\")'". '>Modifier</button>
			<button class="button cancel" onclick='. "'deleteDiscount(\"". $discount['id'] ."\")'" .'>Supprimer</button>
		</span>';
	}

	public function adminEditForm( $discount){
		parent::testRequest();
		return '<form id="form'. $discount['id'] .'">
					<span id="discountInfo'. $discount['id'] .'" class="info">'.
						'Code du rabais:
						<input '. getIdName('newDiscountCode'. $discount['id']).' type="text" class="title" placeholder="Code du rabais" value="'. htmlspecialchars($discount['code']) .'" required">
						Type du rabais:'.
						$this->listTypes($discount['id'])
						.'Valeur du rabais:
						<input '. getIdName('newDiscountValue'. $discount['id']).' type="number" class="amount" placeholder="Valeur du rabais" value="'. $discount['value'] .'" required">
						Date de début:
						<input '. getIdName('newDiscountStart'. $discount['id']).' type="text" class="start" placeholder="Date de début" value="'. $discount['start'] .'" readonly required>
						Date de fin:
						<input '. getIdName('newDiscountEnd'. $discount['id']).' type="text" class="end" placeholder="Date de fin" value="'. $discount['end'] .'" readonly required>'.
						'Âge minimum: (optionnel)
						<input '. getIdName('newDiscountAge'. $discount['id']) .' type="text" placeholder="Age minimum pour le rabais" value="'. $discount['minAge'] .'">
						Description: (optionnel)
						<textarea rows="4" cols="50" '. getIdName('newDiscountDescription'. $discount['id']) .' placeholder="Description du rabais">'. htmlspecialchars($discount['description']) .'</textarea>
						Compagnie: (optionnel)'.
						$this->listCompanies($discount['id']).
					'</span>
					<span id="discountAction'. $discount['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditDiscount(\"" . $discount['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditDiscount(\"" . $discount['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
				$("#companySelector'. $discount['id'] .'").val("'. ($discount['company'] ? $discount['company'] : 'null') .'");
				$("#DiscountTypeSelector'. $discount['id'] .'").val("'. $discount['type'] .'");
					$("#form'. $discount['id'] .'").validate({
					rules:{
						newDiscountCode'. $discount['id'] .':{ minlength : 3},
						newDiscountAmount'. $discount['id'] .':{ min : 1},
						newDiscountAge'. $discount['id'] .':{ min :0}
					},
					messages:{
						newDiscountCode'. $discount['id'] .':{ minlength: "Le code doit être au moins de 3 caractères de long."},
						newDiscountAmount'. $discount['id'] .':{ min : "Un rabais ne peut être moins que 1 dollar ou pourcent"},
						newDiscountAge'. $discount['id'] .':{ min :"Impossible que l\'âge soit négatif."}
					}
					});
				</script>';

	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
						Code du rabais:
						<input '. getIdName('DiscountCode') .' type="text" placeholder="Code du rabais" value="'. generateRandomSequence() .'" required>
						Type du rabais:'.
						$this->listTypes().
						'Valeur du rabais:
						<input '. getIdName('DiscountValue') .' type="number" placeholder="Valeur du rabais" required>
						Date de début:
						<input '. getIdName('DiscountStart') .' type="date" class="start" value="" readonly placeholder="Date de début" required>
						Date de fin:
						<input '. getIdName('DiscountEnd') .' type="date" class="end" value="" readonly placeholder="Date de fin" required>
						Âge minimum: (optionnel)
						<input '. getIdName('DiscountAge') .' type="number" placeholder="Age minimum pour le rabais">
						Description:
						<textarea rows="4" cols="50" '. getIdName('DiscountDescription') .' placeholder="Description du rabais"/>
						Compagnie: (optionnel)'.
						$this->listCompanies().'
					</div>
				</form>
				<script>
    				initDatePickers("#DiscountStart","#DiscountEnd");
    			</script>
    			<script>
				$("#form").validate({
					rules:{
						DiscountCode:{ minlength : 3},
						DiscountAmount:{ min : 1},
						DiscountAge:{ min : 0}
					},
					messages:{
						DiscountCode:{ minlength: "Le code doit être au moins de 3 caractères de long."},
						DiscountAmount:{ min : "Un rabais ne peut être moins que 1 dollar ou pourcent."},
						DiscountAge:{ min : "Impossible que l\'âge soit négatif."}
					}
					});
				</script>';
	}

	/**
	 * @param $discounts PDOStatement
	 * @return array
	 */
	public function createObject($discounts){
		$discountsObjects = [];
		while($discount = $discounts->fetch()){
			$discountsObjects[] = [
				'id' => $discount['id'],
				'code' => $discount['alias'],
				'description' => $discount['description'],
				'type' => $discount['type'],
				'value' => $discount['value'],
				'minAge' => $discount['minimumAge'],
				'start' => $discount['start'],
				'end' => $discount['expiration'],
				'member' => (isset($discount['member_id']) ? $discount['member_id'] : null),
				'company' => (isset($discount['company_id']) ? $discount['company_id'] : null)
			];
		}
		orderBy($discountsObjects,'start');
		return $discountsObjects;
	}

	/**
	 * @param $discount array
	 * @return string
	 */
	private function getMinAge($discount){
		$html = "";
		if(isset($discount['minAge'])){
			$html .= '<span class="title">Âge minimum: ';
			$html .= plurialNoun($discount['minAge'],'an');
			$html .= '</span>';
		}
		return $html;
	}

	/**
	 * @param $discount array
	 * @return string
	 */
	private function getHolder($discount){
		$html = "";
		if((isset($discount['member']) || isset($discount['company']))){
			$html .= '<span class="holder">Détenu par ';

			if(isset($discount['member'])){
				$html .= 'un membre:</br><i>'.$discount['member'] .'</i>';
			 }else{
				$queryString = 'SELECT * from companies where id in (select company_id from discounts where active=true and id='. $discount['id'] .')';
				$actualCompany = DB::getInstance()->customQuery($queryString)->fetch();
				$html .= 'une compagnie:</br><i>'. ( $actualCompany['active'] ? $actualCompany['title'] : '<del style="color: darkred;">' . $actualCompany['title'] . '</del>' ) .'</i>';
			}
			$html .= '</span>';
		}
		return $html;
	}

	private function listTypes($id = "0"){
		$html = "<select id='DiscountTypeSelector".$id ."' name='DiscountTypeSelector'>";
		$html .= '<option value="$">Montant</option>';
		$html .= '<option value="%">Pourcentage</option>';
		$html .= "</select>";
		return $html;
	}

	private function listCompanies($id = "0"){
		$queryString = 'SELECT * from companies where active= true OR id in (select company_id from discounts where active=true)';
		$availablesPackages = DB::getInstance()->customQuery($queryString);

		$html = "<select id='companySelector".$id ."' name='companieSelector'>";
		$options = "<option value='null'>Aucune companie (ou en choisir une)</option>";
		while($companie = $availablesPackages->fetch()){
			$options .= '<option value="'. $companie['id'] .'"'. ( !$companie['active'] ? 'disabled="disabled"' : '' ) .'>'. ( $companie['active'] ? $companie['title'] : $companie['title']) . '</option>';
		}//'. ( !$companie['active'] ? 'disabled="disabled"' : '' ) .'
		$html .= $options . "</select>";
		return $html;
	}

	public function isDiscountAuthorized($code, $packageType){
		$discount = $this->getDiscount($code);
		if($discount){
			$queryString = 'SELECT * FROM spinning.discounts_subscription_types WHERE discount_id=' . $discount[ 'id' ];
			$restrictions = DB::getInstance()->customQuery($queryString);
			if ( $restrictions->rowCount() > 0 ){
				while( $restriction = $restrictions->fetch() ){
					if ( $restriction[ 'subscription_type_id' ] == $packageType ){
						return true;
					}
				}
				return false;
			} else{
				return true;
			}
		}else{
			return false;
		}
	}

	public function getDiscountValue($code, $price){
		if($code !== ''){
			$discount = $this->getDiscount($code);
			$this->checkValidity($discount);
			if ( $discount ){
				switch( $discount[ 'type' ] ){
					case '$':
						return intval($discount[ 'value' ]);
						break;
					case '%':
						return $price * ( intval($discount[ 'value' ]) / 100 );
						break;
					default:
						alert('Désolé, le code rabais entré n\'est pas valide!');
						break;
				}
			} else{
				alert('Désolé, le code rabais entré n\'est pas valide!');
			}
		}
		return 0;
	}

	public function getDiscount($code){
			$queryString = 'SELECT * FROM ' . $this->table . ' WHERE alias="' . $code . '"';
			$discount = DB::getInstance()->customQuery($queryString)->fetch();
			if ( $discount ){
				return $discount;
			} else{
				return false;
			}
	}

	public function getDiscountIdByCode($code){
		if($this->getDiscount($code) !== false){
			return $this->getDiscount($code)[ 'id' ];
		}else{
			return null;
		}
	}

	public function checkValidity($discount){
		$now = time();
		$discountStart = strtotime($discount['start']);
		$discountEnd = strtotime($discount['expiration']);
		if($now < $discountStart){
			alert('Le rabais que vous tentez d\'appliquer ne commence que le ' . formatDateTime($discount['start'], false). '!');
		}
		if($now > $discountEnd){
			alert('Désolé, mais ce rabais a expiré!');
		}
	}

	public function isExpired($discount){
		$now = time();
		$discountEnd = strtotime($discount['end']);
		if($now > $discountEnd){
			return true;
		}
		return false;
	}
}
