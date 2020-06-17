<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-05
 * Time: 09:10
 */
class MemberTypes extends Modules
{
	/**
	 * MemberTypes constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('member_types');
		//$this->toolbar->addOption('Nouveeau type de membre',null,'newMemberType()');
	}

	public function format( $memberType){
		parent::testRequest();

		return '<span id="memberTypeInfo" class="info">
					<span class="title">'. $memberType['title'] .'</span>
				</span>
				<span id="memberTypeAction" class="actions">
				</span>';
	}
	// <button class="button edit"onclick='. "'editMemberType(\"". $memberType['id'] ."\")'". '>Modifier</button>
	// <button class="button cancel" onclick='. "'deleteMemberType(\"". $memberType['id'] ."\")'" .'>Supprimer</button>


	public function adminEditForm( $memberType){
		parent::testRequest();
		return '<form id="form'. $memberType['id'] .'">
					<span id="memberTypeInfo'. $memberType['id'] .'" class="info">'.
						'Titre:
						<input '. getIdName('newMemberTypeTitle'. $memberType['id']).' type="text" class="title" placeholder="Titre du type de membre" value="'. htmlspecialchars ($memberType['title']) .'" required">'.
					'</span>
					<span id="memberTypeAction'. $memberType['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditMemberType(\"" . $memberType['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditMemberType(\"" . $memberType['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
					$("#form'. $memberType['id'] .'").validate({
						rules:{
							newMemberTypeTitle'. $memberType['id'] .':{ minlength : 3,
							required: true},
						},
						messages:{
							newMemberTypeTitle'. $memberType['id'] .':{ minlength: "Le titre doit être au moins de 3 caractères de long.",
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
						<input id="MemberTypeTitle" name="MemberTypeTitle" type="text" placeholder="Titre de la memberType" required>
					</div>
				</form>
				<script>
					today = new Date();
		            nextWeek = today - today.getDay()*DAY_STAMP + WEEK_STAMP;
    				initWeekPickers("#MemberTypeStart","#MemberTypeEnd", new Date(nextWeek));
					$("#form").validate({
						rules:{
							MemberTypeTitle:{ minlength : 3}
						},
						messages:{
							MemberTypeTitle:{ minlength: "Le titre doit être au moins de 3 caractères de long."},
						}
					});
				</script>';
	}

	public function listMemberTypes($id = 0, $init = null){
		$script ='';
		if($init){
			$script = '<script> $("#memberTypeSelector'.$id.'").val('.$init .')</script>';
		}
		$types = $this->getAll();
		$html = "<select id='memberTypeSelector".$id."' name='memberTypeSelector".$id."'>";
		$options = "";
		while($type = $types->fetch()){
			$options .= '<option value="'. $type['id'] .'">'. $type['title'] .'</option>';
		}
		return $html . $options . "</select>" . $script;
	}

	public function parse($id){
		return $this->getOne($id)->fetch()['title'];
	}
}