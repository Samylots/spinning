<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Packages extends Modules
{
	/**
	 * Packages constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('subscription_types');
		$this->adminToolbar->addOption('Nouveau forfait',null,'newPackage()');
	}

	public function format( $package){
		parent::testRequest();
		return '<span id="packageInfo" class="info">
					<span class="title">'. $this->formatTitle($package) .'</span>
				</span>
				<span id="packageAction" class="actions">
					<button class="button edit" onclick='. "'editPackage(\"". $package['id'] ."\")'". '>Modifier</button>
					<button class="button cancel" onclick='. "'deletePackage(\"". $package['id'] ."\")'" .'>Supprimer</button>
				</span>';
	}

	public function adminEditForm( $package){
		parent::testRequest();
		return '<form id="form'. $package['id'] .'">
					<span id="packageInfo'. $package['id'] .'" class="info">'.
						'Nombre de séances permises dans le forfait:
						<input '. getIdName('newMeetings'. $package['id']).' type="number" class="meetings" placeholder="Nombre de séances permises" value="'. $package['meetings_allowed'] .'" required>
						Limite de temps pour s\'inscrire à une séance :
						<input '. getIdName('newLimitStart'. $package['id']).' class="hasWickedpicker" type="text" required readonly value="'. $package['registration_deadline'] .'">
						Limite de temps pour se désinscrire à une séance:
						<input '. getIdName('newLimitCancel'. $package['id']).' class="hasWickedpicker" type="text" required readonly value="'. $package['cancellation_deadline'] .'">
						Est-ce un abonnement?:
						<input type="checkbox" '. getIdName('newWeekly'. $package['id']).' value="1"'. ($package['weekly'] ? ' checked' : '') .'></br>
						Nombre de semaine que l\'on peut s\'inscrire à l\'avance :
						<input '. getIdName('newWeekAdvance'. $package['id']).' type="number" class="weekAdvance" placeholder="Nombre de semaine à l\'avance" value="'. $package['limit_registration_advance'] .'" required>'.
					'</span>
					<span id="packageAction'. $package['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditPackage(\"" . $package['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditPackage(\"" . $package['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
                    initTime("#newLimitStart'. $package['id'] .'","'. $package['registration_deadline'] .'");
                    initTime("#newLimitCancel'. $package['id'] .'","'. $package['cancellation_deadline'] .'");
					$("#form'. $package['id'] .'").validate({
						rules:{
							newMeetings'. $package['id'] .':{ min : 1},
							newWeekAdvance'. $package['id'] .':{ min : 0}
						},
						messages:{
							newMeetings'. $package['id'] .':{ min: "Un forfait doit avoir au moins une séance"},
							newWeekAdvance'. $package['id'] .':{ min: "Le nombre de semaine à l\' avance ne peut être négatif"}
						}
					});
				</script>';

	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
						Nombre de séances permises dans le forfait:
						<input '. getIdName('Meetings').' type="number" class="meetings" placeholder="Nombre de séances permises" required>
						Limite de temps pour s\'inscrire à une séance :
						<input '. getIdName('LimitStart').' class="hasWickedpicker" type="text" placeholder="Heure de début" required readonly>
						Limite de temps pour se désinscrire à une séance:
						<input '. getIdName('LimitCancel').' class="hasWickedpicker" type="text" placeholder="Heure de début" required readonly >
						Est-ce un abonnement?:<input type="checkbox" '. getIdName('Weekly') .' value="1"> </br>
						Nombre de semaine que l\'on peut s\'inscrire à l\'avance :
						<input '. getIdName('WeekAdvance').' type="number" class="weekAdvance" placeholder="Nombre de semaine à l\'avance" required>
					</div>
				</form>
				<script>
                    initTime("#LimitStart","00 : 15");
                    initTime("#LimitCancel","02 : 00");
					$("#form").validate({
						rules:{
							Meetings:{ min : 1},
							WeekAdvance:{ min : 0}
						},
						messages:{
							Meetings:{ min: "Un forfait doit avoir au moins une séance"},
							WeekAdvance:{ min: "Le nombre de semaine à l\' avance ne peut être négatif"}
						}
					});
				</script>';
	}

	/**
	 * Format a title for this package type
	 * @param $package
	 * @return string
	 */
	public static function formatTitle($package){
		if($package['weekly']){
			return $package['meetings_allowed'] . ' X SEMAINE';
		}else{
			return mb_strtoupper(plurialNoun($package['meetings_allowed'], 'séance'),'UTF-8') . ' AU CHOIX';
		}
	}

	public function isACard($packageId){
		return !$this->getOne($packageId)->fetch()['weekly'];
	}

	public function getMeetingsAllowed($cardTypeId){
		return $this->getOne($cardTypeId)->fetch()['meetings_allowed'];
	}
}