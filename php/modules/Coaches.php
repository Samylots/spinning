<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:29
 */
class Coaches extends Modules
{
	/**
	 * Coaches constructor.
	 * @param string $table
	 */
	private $actualMeetingId;

	public function getAll(){
		try {
			return DB::getInstance()->select('members', '*', "id in (select member_id from members_meetings as mm left join members as m on m.id = mm.member_id where
				meeting_id = ".$this->actualMeetingId.") order by lastname"); //and type_id = ". Config::$coachTypeId . "
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getAllAutocomplete($term){
		try {
			return DB::getInstance()->select('members', '*', "id not in (select member_id from members_meetings as mm left join members as m on m.id = mm.member_id where
				meeting_id = ".$this->actualMeetingId.") and (type_id = ". Config::$coachTypeId . " or type_id = ". Config::$administratorTypeId ." )
				and ( firstname like '%". $term ."%' or lastname like '%". $term ."%') order by lastname");
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	public function getAllMeetings($memberId){
		try {
			return DB::getInstance()->select('meetings', '*', "id in (select meeting_id from members_meetings as mm
			left join meetings as m on m.id = mm.meeting_id where member_id =".$memberId." and m.active = true)");
		}catch(PDOException $ex) {
			fail($ex->getMessage());
			return null;
		}
	}

	function __construct($meetingId = null) {
		$this->actualMeetingId = $meetingId;
		parent::__construct('members_meetings');
	}

	public function setMeetingId($meetingId){
		$this->actualMeetingId = $meetingId;
	}

	public function format( $coache){
		fail('NO FORMAT TO DO');
	}

	public function adminEditForm( $coache){
		fail('NO EDIT TO DO');
	}

	public function adminNewForm(){
		fail('NO NEW TO DO');
	}

	public function getCoachName(){
		$coach = $this->getAll()->fetch();
		return $coach['firstname'];
		///return $coach['firstname'] .' '. $coach['lastname'];

	}

	public function listActualCoaches($canEdit = false){
		$actualCoaches = $this->getAll();
		$html ='';
		while($coach = $actualCoaches->fetch()){
			$html .= '<span class="meetingCoach">'. $coach['firstname'] .' '. $coach['lastname'] . '</span>';
			if($canEdit){
				$html .= '<button class="button cancel" onclick=' . "'removeCoach(\"" . $coach[ 'id' ] . "\")'" . '>Retirer</button><br>';
			}
		}
		return $html;
	}

	public function listAvailableCoaches(){
		$queryString = "SELECT * FROM members where id not in (select member_id from members_meetings as mm".
			" left join members as m on m.id = mm.member_id where meeting_id = ".$this->actualMeetingId.") ".
			"and (type_id = ". Config::$coachTypeId . " or type_id = ". Config::$administratorTypeId ." ) order by lastname";
		$availableCoaches = DB::getInstance()->customQuery($queryString);

		$options = "";
		while($coach = $availableCoaches->fetch()){
			$options .= '<option value="'. $coach['id'] .'">'. $coach['firstname'] .' '. $coach['lastname'] . '</option>';
		}
		if($options == ""){
			alert('Désolé, il n\'y a plus d\'entraîneur disponible.');
		}

		$html = '<form id="coachForm"><input '. getIdName('coachSelector').' type="hidden" placeholder="id" required>
		<input '. getIdName('coachAC').' type="text" placeholder="Nom de l\'entraineur" required></form>
		<script>
			$("#coachForm").validate({
				rules:{
					coachAC:{
						required : true}
				},
				messages:{
					coachAC:{
						required : "Vous devez avoir choisi un entraîneur!"}
				}
			});
			$("#coachAC" ).autocomplete({
				minLength: 0,
				source: function( request, response ) {
					$.ajax({
							url: "php/meetings/listCoachesAutocomplete.php",
							dataType: "JSON",
							type: "POST",
							data: {
							q: request.term, meetingId : '.$this->actualMeetingId.'
						},
						success: function( data ) {
							response( data );
						}
					});
					disableCustomButton();
				},
				select: function(event, ui) {
					enableCustomButton();
					$("#coachSelector").val(ui.item.id);
					$("#coachAC").val(ui.item.firstname + " " + ui.item.lastname);
					return false;
				}
			}).autocomplete().data("uiAutocomplete")._renderItem =  function( ul, item ){
				return $( "<li>" )
				.append( "<a>" + item.firstname + " " + item.lastname+ "</a>" )
				.appendTo( ul );
			};
		</script>';

		return $html;
	}

	public function addCoach($memberId){
		$queryString = 'INSERT INTO '. $this->table .'(meeting_id,member_id,active) values('. $this->actualMeetingId .','.$memberId.',1)';
		return (DB::getInstance()->exec($queryString) >= 1);
	}

	public function removeCoach($memberId){
		$queryString = 'DELETE FROM ' . $this->table . ' WHERE meeting_id=' . $this->actualMeetingId . ' AND member_id=' . $memberId;
		return (DB::getInstance()->exec($queryString) >= 1);
	}
}