<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-28
 * Time: 19:29
 */
class Pictures extends Modules
{
	/**
	 * Pictures constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('pictures');
	}

	public function getAll(){
		try {
			return DB::getInstance()->select($this->table, '*');
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}


	public function format( $picture){
		parent::testRequest();
		return '<span id="pictureInfo" class="info">
					<span class="picture">
						<img src="'. $picture['title'] .'" alt="Image" />
					</span>
					<span class="title">'.
						$picture['title']
					.'</span>
				</span>
				<span id="pictureAction" class="actions">
					<button class="button confirm" onclick='. "'choosePicture(\"". $picture['id'] ."\")'" .'>Choisir</button>
					<button class="button cancel" onclick='. "'deletePicture(\"". $picture['id'] ."\")'" .'>Supprimer</button>
				</span>';
	}

	public function adminEditForm( $picture){
		parent::testRequest();
		fail('CANT EDIT PICTURE');
	}

	public function adminNewForm(){
		parent::testRequest();
		return '<form id="form">
					<div class="inputs">
				        Image:
				        <input type="file" id="imgInp" /> <img id="picturePreview" src="#" alt="Image" />
						Titre:
						<input id="PictureTitle" name="PictureTitle" type="text" placeholder="Titre" required>
						Description:
						<textarea rows="4" cols="50" '. getIdName('PictureDescription') .' placeholder="Description"/>
					</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							PictureTitle:{ required: true, minlength : 3}
						},
						messages:{
							PictureTitle:{ minlength: "Le titre doit être au moins de 3 caractères de long."}
						}
					});
				</script>';
	}
}