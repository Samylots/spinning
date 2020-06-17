<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Members extends Modules implements MemberFormatter
{
	/**
	 * Members constructor.
	 * @param string $table
	 */
	function __construct() {
		parent::__construct('members');
		$this->adminToolbar->addOption('Nouveau membre',null,'newMember()');
		$this->adminToolbar->setCustomSearch('
		<input '. getIdName('MemberFilter').' type="hidden" placeholder="id" >
		<input '. getIdName('MemberAC').' type="text" placeholder="Recherche d\'un membre" >
		<script>
		$("#MemberAC" ).autocomplete({
			 minLength: 0,
			 source: function( request, response ) {
					loadAutocompleteMembers(request.term, "MemberAC");
				 }
		});
		 </script>
		');
	}

	public function getAll(){
		try {
			return DB::getInstance()->select('members', '*', "order by lastname");
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getAllAutocomplete($term){
		try {
			return DB::getInstance()->select('members', '*', "firstname like'%" . $term . "%' or lastname like'%" .
				$term . "%' or email like '%" . $term . "%' or phone like '%" . $term . "%' or nickname like '%" .
				$term . "%' or postal_code like '%" . $term . "%' order by lastname");
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getAllWithPurchases(){
		$queryString = 'select m.id, firstname, lastname from members as m inner join subscription_receipts as r on m.id = r.member_id where m.active = true group by m.id';
		return DB::getInstance()->customQuery($queryString);
	}

	public function getAllWithPurchasesAutocomplete($term){
		$queryString = 'select m.id, firstname, lastname, email, phone from members as m inner join subscription_receipts as r on m.id = r.member_id where m.active = true
 						and ( firstname like "%'. $term .'%" or lastname like "%'. $term .'%" or email like "%'. $term .'%" or phone like "%'. $term .'%" ) group by m.id';
		return DB::getInstance()->customQuery($queryString);
	}

	public function add( $data ){
		if($data['email']){
			$this->checkIfEmailAlreadyUsed(null,$data['email']);
		}
		return parent::add($data);
	}

	public function edit( $id, $data, $debug = false ){
		if(isset($data['email'])){
			$this->checkIfEmailAlreadyUsed($id,$data['email']);
		}
		return parent::edit($id, $data, $debug);
	}


	public function format( $member){
		parent::testRequest();
		$memberTypeModule = new MemberTypes();
		return '<span id="memberInfo" class="info">
					<span class="fullname">'. $member['firstname'] .' '. $member['lastname'] .'</span>
					<span class="alias"> ('. $member['nickname'] .')</span>
					<span class="type"> ('. $memberTypeModule->parse($member['type_id']) .')</span></br>
					<span class="email">'. $member['email'] .'</span></br>
					<span class="phone">'. formatPhone($member['phone']) .'</span></br>
					<span class="postalCode">'. formatPostalCode($member['postal_code']) .'</span></br>
					<span class="age"> ('. plurialNoun($this->getAge($member['birthdate']), 'an') .')</span>
					<span class="gender"> ('. Helper::getGender($member['gender']) .')</span>
				</span>
				<span id="memberAction" class="actions">
					<button class="button confirm" onclick='. "'manageMember(\"". $member['id'] ."\")'" .'>Gérer</button>
					<button class="button edit" onclick='. "'editMember(\"". $member['id'] ."\")'" . '>Modifier</button>
					<button class="button cancel" onclick='. "'deleteMember(\"". $member['id'] ."\")'" .'>Supprimer</button>
				</span>';
	}

	public function adminEditForm( $member){
		parent::testRequest();
		$memberTypeModule = new MemberTypes();
		return '<form id="form'. $member['id'] .'">
					<span id="memberInfo'. $member['id'] .'" class="info">'.
						'Prénom:
						<input '. getIdName('newMemberFirstname'. $member['id']).' type="text" class="title" placeholder="Prénom" value="'. htmlspecialchars ($member['firstname']) .'" required">
						Nom:
						<input '. getIdName('newMemberLastname'. $member['id']).' type="text" class="title" placeholder="Nom" value="'. htmlspecialchars ($member['lastname']) .'" required">
						Email:
						<input '. getIdName('newMemberEmail'. $member['id']).' type="email" class="title" placeholder="Email" value="'. htmlspecialchars ($member['email']) .'" required">
						Téléphone:
						<input '. getIdName('newMemberPhone'. $member['id']).' type="phone" class="title" placeholder="Téléphone" value="'. htmlspecialchars ($member['phone']) .'" required">
						Code postal:
						<input '. getIdName('newMemberPostalCode'. $member['id']).' type="text" class="title" placeholder="Code postal" value="'. htmlspecialchars ($member['postal_code']) .'" required">
						Nom d\'utilisateur:
						<input '. getIdName('newMemberUsername'. $member['id']).' type="text" class="title" placeholder="Nom d\'utilisateur" value="'. htmlspecialchars ($member['nickname']) .'" required">
						Genre:'.
						Helper::listGender($member['id'],$member['gender']).
						'Date de naissance:
						<span class="date">'. Helper::getDateSelector($member['id'], $member['birthdate']). '</span>'.
						'Type du membre:'.
						$memberTypeModule->listMemberTypes($member['id'], $member['type_id']) .
					'</span>
					<span id="memberAction'. $member['id'] .'" class="actions">'.
						'<button class="button confirm" onclick=' . "'saveEditMember(\"" . $member['id'] . "\")'" . '>Enregistrer</button>' .
						'<button class="button cancel" onclick='. "'cancelEditMember(\"" . $member['id'] . "\")'" .'>Annuler</button>'.
					'</span>
				</form>
				<script>
					$("#form'. $member['id'] .'").validate({
						rules:{
							newMemberFirstname'. $member['id'] .':{
								minlength : 3,
								required: true},
							newMemberPostalCode'. $member['id'] .':{
								required: true,
								postalCode: true},
							newMemberLastname'. $member['id'] .':{
								required : true,
								minlength : 3},
							newMemberEmail'. $member['id'] .':{
								required : true,
								email : true},
							newMemberPhone'. $member['id'] .':{
								required : true,
								phoneUS : true},
							newMemberUsername'. $member['id'] .':{
								required : true,
					            minlength : 5}
						},
						messages:{
							newMemberFirstname'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								minlength: "Votre prenom doit être au moins de 3 caractère de long."},
							newMemberLastname'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								minlength: "Votre nom doit être au moins de 3 caractère de long."},
							newMemberEmail'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								email: "Votre email doit être valide!"},
							newMemberPhone'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								phoneUS: "Votre numéro de téléphone doit être valide!."}
						}
					});
				</script>';
	}

	public function adminNewForm(){
		//value="'. generateRandomSequence() .'"
		parent::testRequest();
		$memberTypeModule = new MemberTypes();
		return '<form id="form">
					<div class="inputs">
						Prénom:
						<input '. getIdName('newMemberFirstname').' type="text" class="title" placeholder="Prénom"  required">
						Nom:
						<input '. getIdName('newMemberLastname').' type="text" class="title" placeholder="Nom"  required">
						Email:
						<input '. getIdName('newMemberEmail').' type="email" class="title" placeholder="Email"  required">
						Téléphone:
						<input '. getIdName('newMemberPhone').' type="phone" class="title" placeholder="Téléphone" required">
						Code postal:
						<input '. getIdName('newMemberPostalCode').' type="text" class="title" placeholder="Code postal"  required">
						Nom d\'utilisateur:
						<input '. getIdName('newMemberUsername').' type="text" class="title" placeholder="Nom d\'utilisateur" required">
						Genre:'.
						Helper::listGender().
						'Date de naissance:
						<span class="date">'. Helper::getDateSelector(). '</span>'.
						'Type du membre:'.
						$memberTypeModule->listMemberTypes() .
					'</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							newMemberFirstname:{ minlength : 3,
							required: true},
							newMemberLastname:{
								required : true,
								minlength : 3},
							newMemberPostalCode:{
								required: true,
								postalCode: true},
							newMemberEmail:{
								required : true,
								email : true},
							newMemberPhone:{
								required : true,
								phoneUS : true},
							newMemberUsername:{
								required : true,
					            minlength : 5}
						},
						messages:{
							newMemberFirstname:{
								required : "Ce champ est obligatoire!",
								minlength: "Votre prenom doit être au moins de 3 caractère de long."},
							newMemberLastname:{
								required : "Ce champ est obligatoire!",
								minlength: "Votre nom doit être au moins de 3 caractère de long."},
							newMemberEmail:{
								required : "Ce champ est obligatoire!",
								email: "Votre email doit être valide!"},
							newMemberPhone:{
								required : "Ce champ est obligatoire!",
								phoneUS: "Votre numéro de téléphone doit être valide!."},
							newMemberUsername:{
								required : "Ce champ est obligatoire!",
								minlength: "Votre nom d\'utilisateur doit être au moins de 5 caractère de long."}
						}
					});
				</script>';
	}

	public function loginForm(){
		return '<form id="form">
					<div class="inputs">
						Email:
						<input '. getIdName('loginMemberEmail').' type="email" placeholder="Email" required">
						Mot de passe:
						<input '. getIdName('loginMemberPassword').' type="password" placeholder="Mot de passe" required">'.
					'</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							loginMemberEmail:{
							required: true},
							loginMemberPassword:{
								required : true}
						},
						messages:{
							loginMemberEmail:{
								required : "Ce champ est obligatoire!"},
							loginMemberPassword:{
								required : "Ce champ est obligatoire!"}
						}
					});
				</script>';
	}

	public function registrationForm(){
		return '<form id="form">
					<div class="inputs">
						Prénom:
						<input '. getIdName('newMemberFirstname').' type="text" placeholder="Prénom" required">
						Nom:
						<input '. getIdName('newMemberLastname').' type="text" placeholder="Nom" required">
						Email:
						<input '. getIdName('newMemberEmail').' type="email" placeholder="Email" required">
						Téléphone:
						<input '. getIdName('newMemberPhone').' type="phone" placeholder="Téléphone" required">
						Code postal:
						<input '. getIdName('newMemberPostalCode').' type="text" class="title" placeholder="Code postal"  required">
						Nom d\'utilisateur:
						<input '. getIdName('newMemberUsername').' type="text" placeholder="Nom d\'utilisateur" required">
						Mot de passe:
						<input '. getIdName('newMemberPassword').' type="password" placeholder="Mot de passe" required">
						Confirmation du mot de passe:
						<input '. getIdName('newMemberPasswordConfirm').' type="password" placeholder="Confirmation" required">
						Genre:'.
						Helper::listGender().
						'Date de naissance:
						<span class="date">'. Helper::getDateSelector(). '<span>'.
					'</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							newMemberFirstname:{ minlength : 3,
							required: true},
							newMemberLastname:{
								required : true,
								minlength : 3},
							newMemberEmail:{
								required : true,
								email : true},
							newMemberPhone:{
								required : true,
								phoneUS : true},
							newMemberPostalCode:{
								required: true,
								postalCode: true},
							newMemberUsername:{
								required : true,
					            minlength : 5},
					        newMemberPassword:{
								required : true,
					            minlength : 6},
							newMemberPasswordConfirm:{
								required : true,
								equalTo: "#newMemberPassword"}
						},
						messages:{
							newMemberFirstname:{
								required : "Ce champ est obligatoire!",
								minlength: "Votre prenom doit être au moins de 3 caractère de long."},
							newMemberLastname:{
								required : "Ce champ est obligatoire!",
								minlength: "Votre nom doit être au moins de 3 caractère de long."},
							newMemberEmail:{
								required : "Ce champ est obligatoire!",
								email: "Votre email doit être valide."},
							newMemberPhone:{
								required : "Ce champ est obligatoire!",
								phoneUS: "Votre numéro de téléphone doit être valide."},
							newMemberUsername:{
								required : "Ce champ est obligatoire!",
								minlength: "Votre nom d\'utilisateur doit être au moins de 5 caractère de long."},
							newMemberPassword:{
								required : "Ce champ est obligatoire!",
					            minlength : "Votre mot de passe doit être au moins de 6 caractère de long."},
							newMemberPasswordConfirm:{
								required : "Ce champ est obligatoire!",
								equalTo: "La confirmation du mot de passe doit être identique au mot de passe."}
						}
					});
				</script>';
	}

	public function getAge($birthdate){
		$date = strtotime($birthdate);
		$today = time();
		$diff = $today - $date;
		return floor($diff/YEAR_STAMP);
	}

	public function listMembers(){
		$members = $this->getAll();
		$html = "<select id='memberSelector'>";
		$options = "";
		while($member = $members->fetch()){
			$options .= '<option value="'. $member['id'] .'">'. $member['firstname'] .' '. $member['lastname'] . '</option>';
		}
		return $html . $options . "</select>";
	}

	public function getFullName($member){
		return $member['firstname'] .' '. $member['lastname'];
	}

	public function isACoach($memberId){
		$member = $this->getOne($memberId)->fetch();
		return $member['type_id'] == Config::$coachTypeId;
	}

	/**
	 * @param $memberId
	 * @return array|bool
	 */
	public function isGivingMeetingInFuture($memberId){
		$now = time();
		$coaches = new Coaches();
		$meetings = new Meetings();
		$futureMeetings = $coaches->getAllMeetings($memberId);
		$meetingsCoaching = [];
		while($meeting = $futureMeetings->fetch()){
			if($now < $meetings->getDateTime($meeting['id'])->getTimestamp()){
				$meetingsCoaching[] = $meeting;
			}
		}
		if(!empty($meetingsCoaching)){
			return $meetingsCoaching;
		}
		return false;
	}

	public function checkIfWasACoach( $memberId, $type){
		$meetings = new Meetings();
		if(($this->isACoach($memberId) && $type != Config::$coachTypeId) || $type == null){
			$result = $this->isGivingMeetingInFuture($memberId);
			if($result == false){
				return true;
			}else{
				$error = 'Attention! Ce membre est actuellement un entraîneur et celui-ci devait donner le(s) cours suivant(s): <br><br>';
				$error .= '<div id="actualMeetingsCoaching" class=""item">';
				foreach($result as $meeting){
					$error .= $meetings->formatMeetingTitleTime($meeting['id']);
					$error .= '<button class="button cancel" onclick="' . 'removeMemberCoach(' . $memberId. ",". $meeting['id'] .')' . '">Retirer</button>';
					$error .= '<br>';
				}
				$error .= '</div>';
				$error .= 'Afin de pouvoir le retirer en tant qu\'entraîneur, vous devrez le retirer de ce/ces cours!
							<br> Après l\'avoir retiré des cours, vous devrez fermer ce message et essayer à nouveau
							d\'entregistrer ne membre.';
				alert($error);
			}
		}
		return true;
	}

	public function checkIfEmailAlreadyUsed($id, $email){
		$user = DB::getInstance()->select('members','*','email=\''.  $email .'\'')->fetch();
		if($user && $user['id'] != $id){
			alert('Désolé, l\'email est déjà utilisé par un autre membre...');
			return null;
		}
	}

	public function getFutureMeetingsCoaching($memberId){
		$meetings = new Meetings();
		$result = $this->isGivingMeetingInFuture($memberId);
		$html = '';
		if($result == false){
			return '';
		}
		foreach($result as $meeting){
			$html .= $meetings->formatMeetingTitleTime($meeting['id']);
			$html .= '<button class="button cancel" onclick="' . 'removeMemberCoach(' . $memberId. ",". $meeting['id'] .')' . '">Retirer</button>';
			$html .= '<br>';
		}
		return $html;
	}

	public function memberFormat( $member ){
		$memberTypeModule = new MemberTypes();
		return '<span id="memberInfo" class="info">
					<span class="fullname">Nom complet : '. $member['firstname'] .' '. $member['lastname'] .'</span><br>
					<span class="alias">Nom d\'utilisateur : '. $member['nickname'] .'</span></br>
					<span class="email">Email : '. $member['email'] .'</span></br>
					<span class="phone">Téléphone : '. formatPhone($member['phone']) .'</span></br>
					<span class="postalCode">Code postal : '. formatPostalCode($member['postal_code']) .'</span></br>
					<span class="age">Âge : '. plurialNoun($this->getAge($member['birthdate']), 'an') .'</span></br>
					<span class="gender">Genre : '. Helper::getGender($member['gender']) .'</span>
				</span>
				<span id="memberAction" class="actions">
					<button class="button edit" onclick='. "'editPublicMember(\"". $member['id'] ."\")'" . '>Modifier mes informations</button>
					<button class="button edit" onclick='. "'changePassword(\"". $member['id'] ."\")'" . '>Modifier mon mot de passe</button>
				</span>';
	}

	public function memberEdit($member){
		parent::testRequest();
		return '<form id="form'. $member['id'] .'">
					<span id="memberInfo'. $member['id'] .'" class="info">'.
						'Prénom:
						<input '. getIdName('newMemberFirstname'. $member['id']).' type="text" class="title" placeholder="Prénom" value="'. htmlspecialchars ($member['firstname']) .'" required">
						Nom:
						<input '. getIdName('newMemberLastname'. $member['id']).' type="text" class="title" placeholder="Nom" value="'. htmlspecialchars ($member['lastname']) .'" required">
						Email:
						<input '. getIdName('newMemberEmail'. $member['id']).' type="email" class="title" placeholder="Email" value="'. htmlspecialchars ($member['email']) .'" required">
						Téléphone:
						<input '. getIdName('newMemberPhone'. $member['id']).' type="phone" class="title" placeholder="Téléphone" value="'. htmlspecialchars ($member['phone']) .'" required">
						Code postal:
						<input '. getIdName('newMemberPostalCode'. $member['id']).' type="text" class="title" placeholder="Code postal" value="'. htmlspecialchars ($member['postal_code']) .'" required">
						Nom d\'utilisateur:
						<input '. getIdName('newMemberUsername'. $member['id']).' type="text" class="title" placeholder="Nom d\'utilisateur" value="'. htmlspecialchars ($member['nickname']) .'" required">
						Genre:'.
						Helper::listGender($member['id'],$member['gender']).
						'Date de naissance:
						<span class="date">'. Helper::getDateSelector($member['id'], $member['birthdate']). '</span>'.
					'</span>
					<span id="memberAction'. $member['id'] .'" class="actions">'.
		'<button class="button confirm" onclick=' . "'saveEditPublicMember(\"" . $member['id'] . "\")'" . '>Enregistrer</button>' .
		'<button class="button cancel" onclick='. "'cancelEditPublicMember(\"" . $member['id'] . "\")'" .'>Annuler</button>'.
		'</span>
				</form>
				<script>
					$("#form'. $member['id'] .'").validate({
						rules:{
							newMemberFirstname'. $member['id'] .':{
								minlength : 3,
								required: true},
							newMemberPostalCode'. $member['id'] .':{
								required: true,
								postalCode: true},
							newMemberLastname'. $member['id'] .':{
								required : true,
								minlength : 3},
							newMemberEmail'. $member['id'] .':{
								required : true,
								email : true},
							newMemberPhone'. $member['id'] .':{
								required : true,
								phoneUS : true},
							newMemberUsername'. $member['id'] .':{
								required : true,
					            minlength : 5}
						},
						messages:{
							newMemberFirstname'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								minlength: "Votre prenom doit être au moins de 3 caractère de long."},
							newMemberLastname'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								minlength: "Votre nom doit être au moins de 3 caractère de long."},
							newMemberEmail'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								email: "Votre email doit être valide!"},
							newMemberPhone'. $member['id'] .':{
								required : "Ce champ est obligatoire!",
								phoneUS: "Votre numéro de téléphone doit être valide!."}
						}
					});
				</script>';
	}

	public function checkIfWasOldPassword($id, $oldPassword){
		$member = $this->getOne($id)->fetch();
		if(!password_verify($oldPassword, $member['password'])){
			alert('Attention!, Nous ne pouvons pas changer votre mot de passe, car l\'ancien mot de passe ne correspond pas!');
			return false;
		}
		return true;
	}

	public function adminEditPasswordForm(){
		return '<form id="form">
					<div class="inputs">
						Nouveau mot de passe:
						<input '. getIdName('newMemberPassword').' type="password" placeholder="Nouveau mot de passe" required">
						Confirmation du nouveau mot de passe:
						<input '. getIdName('newMemberPasswordConfirm').' type="password" placeholder="Confirmation du nouveau mot de passe" required">'.
					'</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
					        newMemberPassword:{
								required : true,
					            minlength : 6},
							newMemberPasswordConfirm:{
								required : true,
								equalTo: "#newMemberPassword"}
						},
						messages:{
							newMemberPassword:{
								required : "Ce champ est obligatoire!",
					            minlength : "Votre nouveau mot de passe doit être au moins de 6 caractère de long."},
							newMemberPasswordConfirm:{
								required : "Ce champ est obligatoire!",
								equalTo: "La confirmation du nouveau mot de passe doit être identique au nouveau mot de passe."}
						}
					});
				</script>';
	}

	public function editPasswordForm(){
		return '<form id="form">
					<div class="inputs">
						Ancien mot de passe:
						<input '. getIdName('oldMemberPassword').' type="password" placeholder="Ancien mot de passe" required">
						Nouveau mot de passe:
						<input '. getIdName('newMemberPassword').' type="password" placeholder="Nouveau mot de passe" required">
						Confirmation du nouveau mot de passe:
						<input '. getIdName('newMemberPasswordConfirm').' type="password" placeholder="Confirmation du nouveau mot de passe" required">'.
					'</div>
				</form>
				<script>
					$("#form").validate({
						rules:{
							oldMemberPassword:{
								required : true,
					            minlength : 6},
					        newMemberPassword:{
								required : true,
					            minlength : 6},
							newMemberPasswordConfirm:{
								required : true,
								equalTo: "#newMemberPassword"}
						},
						messages:{
							oldMemberPassword:{
								required : "Ce champ est obligatoire!",
					            minlength : "Votre ancien mot de passe était au moins de 6 caractère de long."},
							newMemberPassword:{
								required : "Ce champ est obligatoire!",
					            minlength : "Votre nouveau mot de passe doit être au moins de 6 caractère de long."},
							newMemberPasswordConfirm:{
								required : "Ce champ est obligatoire!",
								equalTo: "La confirmation du nouveau mot de passe doit être identique au nouveau mot de passe."}
						}
					});
				</script>';
	}

	public function isAdmin($memberId){
		$member = $this->getOne($memberId)->fetch();
		return $member['type_id'] == Config::$administratorTypeId;
	}
}