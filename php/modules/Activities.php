<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Activities extends Modules
{
	private $imgPath = "img/activities/";
	/**
	 * Activities constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('activities');
		$this->adminToolbar->addOption('Nouveau cours',null,'newActivity()');
	}

	public function getAll(){
		return DB::getInstance()->customQuery('SELECT ac.id, ac.title, ac.active, ac.units, types.id
			as typeId,types.color, actypes.time, types.places, pic.url, types.title
			as typeTitle FROM activities as ac
			left join activities_activity_types
			as actypes on actypes.activity_id = ac.id  left join activity_types
			as types on types.id = actypes.activity_type_id left join pictures
			as pic on pic.id = types.id WHERE ac.active = true order by title, id');
	}

	public function getOne( $id, $usingActive = true,  $debug = false ){
		return DB::getInstance()->customQuery('SELECT ac.id, ac.title, ac.active, ac.units, types.id
			as typeId,types.color, actypes.time, types.places, pic.url, types.title
			as typeTitle FROM activities as ac
			left join activities_activity_types
			as actypes on actypes.activity_id = ac.id  left join activity_types
			as types on types.id = actypes.activity_type_id left join pictures
			as pic on pic.id = types.id WHERE ac.active = true AND ac.id = ' . $id . ' order by title, id');
	}


	public function format( $activity){
		parent::testRequest();
		$activityObject = $this->createObject($activity);
		return '<span class="icons">'.
		$this->getIcons($activityObject['types'])
		.'</span>
		<span id="activityInfo'. $activityObject['id'] .'" class="info">
		<span class="title">'.
			$activityObject['title'] . " (" . $activityObject['units'] . " unités)"
		.'</span>
		<span class="info">'.
			$this->getTypes($activityObject['types'])
		.'</span>
		</span>
		<span id="activityAction'. $activityObject['id'] .'" class="actions">
			<button class="button edit"onclick='. "'editActivity(\"". $activityObject['id'] ."\")'" .'>Modifier</button>
			<button class="button cancel" onclick='. "'deleteActivity(\"". $activityObject['id'] ."\")'" .'>Supprimer</button>
		</span>';
	}


	public function adminEditForm( $activity){
		parent::testRequest();
		$activityObject = $this->createObject($activity);
		return '<form id="form'. $activityObject['id'] .'">
					<span id="activityInfo'. $activityObject['id'] .'" class="info">'.
						'Titre:
						<input '. getIdName('newActivityTitle'. $activityObject['id']) .'type="text" class="" placeholder="Titre du cours" value="'. htmlspecialchars($activityObject['title']) .'" required >
						Coût du cours (en unités):
						<input '. getIdName('newActivityUnits'. $activityObject['id']) .'type="number" class="start" placeholder="Unités du cours" value="'. $activityObject['units'] .'" required min="0" step="0.5">
						Activités pratiquées:
						<ul class="activityTypes item">'.
							'<div id="activityTypes'. $activityObject['id'] .'" >'.
								$this->showActivityTypes($activityObject['id'], $activityObject['types'])
							.'</div>
						</ul>
					</span>
					<span id="activityAction'. $activityObject['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditActivity(\"" . $activityObject['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditActivity(\"" . $activityObject['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
				$("#form'. $activityObject['id'] .'").validate({
					rules:{
						newActivityTitle'. $activityObject['id'] .':{
							required : true,
							minlength : 3},
						newActivityUnits'. $activityObject['id'] .':{
							required : true,
				            min : 0}
					},
					messages:{
						newActivityTitle'. $activityObject['id'] .':{ required : "Ce champ est obligatoire!",
									minlength: "Le titre doit être au moins de 3 caractères de long."},
						newActivityUnits'. $activityObject['id'] .':{ required : "Ce champ est obligatoire!",
									min: "L\'unité doit être au moins de 0."}
					}
					});
				</script>';
	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
						Titre:
						<input id="ActivityTitle" name="ActivityTitle" type="text" placeholder="Titre du cours" required>
						Coût du cours (en unités):
						<input id="ActivityUnits" name="ActivityUnits" type="number" value="0" placeholder="Unités du cours" required min="0" step="0.5">
					</div>
				</form>
				<script>
				$("#form").validate({
					rules:{
						ActivityTitle:{
							required : true,
							minlength : 3},
						ActivityUnits:{
							required : true,
				            min : 0}
					},
					messages:{
						ActivityTitle:{ required : "Ce champ est obligatoire!",
									minlength: "Le titre doit être au moins de 3 caractères de long."},
						ActivityUnits:{ required : "Ce champ est obligatoire!",
									min: "L\'unité doit être au moins de 0."}
					}
					});
				</script>';
	}

	public function showActivityTypes($id, $types){
		$html = '<li id="activityTypesAction">'.
				'<button class="button confirm" onclick=' . "'selectTypeToAdd(\"" . $id . "\")'" . '>Ajouter une activité</button>' .
		'</li>';
		foreach($types as $type){
			$html .= $this->formatActivityType($id, $type['id'],$type['pic'],$type['title'],$type['places']);
		}
		return $html;
	}

	private function formatActivityType($id, $idType, $icon, $title, $places){
		return '<li class="item">
					<span class="icons">
						<img src="'. $this->imgPath . $icon .'" alt="Icon"/>
					</span>
					<span id="title">
						'. $title .'
					</span>
					<span id="places">'.
						plurialNoun($places, 'place') .'
					</span>'.
					'<button class="button cancel actions" onclick='. "'removeType(\"" .$id . '","'. $idType . "\")'" .'>Retirer</button>
				</li>';
	}

	/**
	 * @param $activity PDOStatement
	 * @return array|null
	 */
	public function createObject($activity){
		$actualId = null;
		$activityObject = null;
		while($activityType = $activity->fetch()){
			if($actualId != $activityType['id']){
				$actualId = $activityType['id'];
				$activityObject = [
					'id' => $activityType['id'],
					'title' => $activityType['title'],
					'units' => $activityType['units'],
					'types' => []
				];
			}
			if($activityType[ 'typeId' ] && $activityType[ 'typeTitle' ] && $activityType[ 'places' ]){
				$activityObject[ 'types' ][] = [
					'id' => $activityType[ 'typeId' ],
					'title' => $activityType[ 'typeTitle' ],
					'places' => $activityType[ 'places' ],
					'pic' => $activityType[ 'url' ],
					'time' => $activityType[ 'time' ],
					'color' => $activityType[ 'color' ]
				];
			}
		}
		orderBy($activityObject[ 'types' ],'time');
		return $activityObject;
	}

	private function getIcons($types){
		$html = '';
		foreach($types as $type){
			if($type[ 'pic' ]){
				$html .= '<img src="' . $this->imgPath . $type[ 'pic' ] . '" alt="Icon" />';
			}
		};
		return $html;
	}

	private function getTypes($types){
		if(empty($types)){
			return "";
		}
		$infos = [];
		foreach($types as $type){
			$infos[] = $type['title'] . ': ' . plurialNoun($type['places'], 'place');
		};
		return  '(' . implode(', ', $infos) . ')';

	}

	public function getMinPlaces($activityId){
		$activity_types = $this->getOne($activityId);
		$min = 99999;
		while($type =  $activity_types->fetch()){
			$min = min($min, $type['places']);
		}
		return $min;
	}

	public function listActivities(){
		$availableActivities = $this->getAll();
		$actualId = null;
		$html = "Activité:
				<select id='ActivitySelector' name='ActivitySelector'>";
		$options = "";
		while($activityRow = $availableActivities->fetch()){
			if($actualId != $activityRow['id']){
				$actualId = $activityRow['id'];
				$activityObject = $this->createObject($this->getOne($actualId));
				$options .= '<option value="' . $activityObject[ 'id' ] . '">' . $activityObject[ 'title' ] .'</option>';
			}
		}
		$html .= $options . "</select>";
		return $html;
	}
}