<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-29
 * Time: 08:29
 */
class Companies extends Modules
{
	/**
	 * Companies constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('companies');
		$this->adminToolbar->addOption('Nouvelle compagnie',null,'newCompanie()');
	}

	public function format( $session){
		parent::testRequest();

		return '<span id="sessionInfo" class="info">
			<span class="title">'.
		$session['title']
		.'</span>
		</span>
		<span id="sessionAction" class="actions">
			<button class="button edit"onclick='. "'editCompanie(\"". $session['id'] ."\")'" .'>Modifier</button>
			<button class="button cancel" onclick='. "'deleteCompanie(\"". $session['id'] ."\")'" .'>Supprimer</button>
		</span>';
	}

	public function adminEditForm( $session){
		parent::testRequest();
		return '<form id="form'. $session['id'] .'">
					<span id="sessionInfo'. $session['id'] .'" class="info">'.
						'Titre:
						<input '. getIdName('newCompanieTitle'. $session['id']).' type="text" class="title" placeholder="Titre de la compagnie" value="'. htmlspecialchars ($session['title']) .'" required">'.
						'</span>
					<span id="sessionAction'. $session['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditCompanie(\"" . $session['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditCompanie(\"" . $session['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
					$("#form'. $session['id'] .'").validate({
						rules:{
							newCompanieTitle'. $session['id'] .':{ minlength : 3,
							required: true},
						},
						messages:{
							newCompanieTitle'. $session['id'] .':{ minlength: "Le titre doit être au moins de 3 caractères de long.",
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
						<input id="CompanieTitle" name="CompanieTitle" type="text" placeholder="Titre de la compagnie" required>
					</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							CompanieTitle:{ minlength : 3}
						},
						messages:{
							CompanieTitle:{ minlength: "Le titre doit être au moins de 3 caractères de long."},
						}
					});
				</script>';
	}

}