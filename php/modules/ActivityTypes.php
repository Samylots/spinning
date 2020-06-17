<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class ActivityTypes extends Modules
{
	private $imgPath = "img/activities/";
	/**
	 * ActivityTypes constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('activity_types');
		$this->adminToolbar->addOption('Nouvelle activité',null,'newActivityType()');
	}

	public function getAll(){
		return DB::getInstance()->customQuery('SELECT type.id, type.description, type.title, type.places, type.color,
				type.active, pic.url FROM '. $this->table . ' as type left join pictures
				as pic on pic.id = type.picture_id WHERE type.active = true order by title');
	}

	public function getOne( $id, $usingActive = true,  $debug = false ){
		return DB::getInstance()->customQuery('SELECT type.id, type.description,type.title, type.places, type.color,
				type.active, pic.url FROM '. $this->table . ' as type left join pictures
				as pic on pic.id = type.picture_id WHERE type.active = true AND type.id ='. $id . ' order by title');
	}

	public function format( $activity){
		parent::testRequest();
		return '<span class="icon">
					<img src="'. $this->imgPath . $activity['url'] .'"/>
				</span>
				<span id="activityTypeInfo" class="info">
					<span class="title">'.
					$activity['title']
					.'</span>
						<span class="places">'.
							plurialNoun($activity['places'],'place')
					.'  </span>
						<div class="colorShower" style="background-color :'. $activity['color'] .';"></div>'.
				'</span>
				<span id="activityTypeAction" class="actions">
					<button class="button edit"onclick='. "'editActivityType(\"". $activity['id'] ."\")'" .'>Modifier</button>
					<button class="button cancel" onclick='. "'deleteActivityType(\"". $activity['id'] ."\")'" .'>Supprimer</button>
				</span>';
	}

	public function adminEditForm( $activity){
		parent::testRequest();
		return '<form id="form'. $activity['id'] .'">
					<span id="activityTypeInfo'. $activity['id'] .'" class="info">'.
						'Titre:
						<input '. getIdName('newActivityTypeTitle'. $activity['id']) .' type="text" class="title" placeholder="Titre de l\'activité" value="'. htmlspecialchars($activity['title']) .'">'.
						'Nombre de places disponibles:
						<input '. getIdName('newActivityTypePlaces'. $activity['id']) .' type="number" placeholder="Nombre de places" value="'. $activity['places'] .'"/>'.
						'Couleur d\'affichage dans l\'horaire:
						<input '. getIdName('colorSelector'. $activity['id']) .' class="colorPicker" type="text" class="basic" value="'. $activity['color'] .'"/>'.
						'Description affichée du type d\'activité:
						<textarea rows="4" cols="50" '. getIdName('newActivityTypeDescription'. $activity['id']) .'" placeholder="Description du type d\'activité">'. htmlspecialchars($activity['description']) .'</textarea>'.
					'</span>
					<span id="activityTypeAction'. $activity['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditActivityType(\"" . $activity['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditActivityType(\"" . $activity['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
					$("#form'. $activity['id'] .'").validate({
						rules:{
							newActivityTypeTitle'. $activity['id'] .':{ minlength : 3,
							required: true},
							newActivityTypePlaces'. $activity['id'] .':{ min : 0,
							required: true},
						},
						messages:{
							newActivityTypeTitle'. $activity['id'] .':{ minlength: "Le titre doit être au moins de 3 caractères de long.",
							required: "Ce champ est obligatoire!"},
							newActivityTypePlaces'. $activity['id'] .':{ min: "Le nombre de place ne peut être négatif.",
							required: "Ce champ est obligatoire!"},
						}
						});
						$("#colorSelector'. $activity['id'] .'").spectrum({
		                color: "#fff",
		                change: function(color) {
		                    $("#basic-log").text("change called: " + color.toHexString());
		                }
		            });
				</script>';

	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
						Titre:
						<input '. getIdName('newActivityTypeTitle') .' type="text" class="title" placeholder="Titre de l\'activité">
						Nombre de places disponibles:
						<input '. getIdName('newActivityTypePlaces') .' type="number" placeholder="Nombre de places"/>
						Couleur d\'affichage dans l\'horaire:
					    <input '. getIdName('colorSelector') .'  class="colorPicker" type="text"/>
					    Description affichée du type d\'activité:
						<textarea rows="4" cols="50" '. getIdName('newActivityTypeDescription') .' placeholder="Description du type d\'activité"/>
					</div>
				</form>
				<script>
					$("#form").validate({
					rules:{
						newActivityTypeTitle:{ minlength : 3,
						required: true},
						newActivityTypePlaces:{ min : 0,
						required: true},
					},
					messages:{
						newActivityTypeTitle:{ minlength: "Le titre doit être au moins de 3 caractères de long.",
						required: "Ce champ est obligatoire!"},
						newActivityTypePlaces:{ min: "Le nombre de place ne peut être négatif.",
						required: "Ce champ est obligatoire!"},
					}
					});
					$("#colorSelector").spectrum({
		                color: "#fff",
		                change: function(color) {
		                    console.log(color);
		                    $("#basic-log").text("change called: " + color.toHexString());
		                }
		            });
    			</script>';
	}
}
