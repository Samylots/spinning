<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-31
 * Time: 08:14
 */
class Helper
{
	private static $minAge = 5;
	private static $maxAge = 90;
	public static $days = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];

	public static function getDateSelector($id = "0", $init = null){

		return self::listDays(1,1,$id).self::listMonths($id).self::listYears($id).self::getScript($id, $init);
	}

	public static function getDaysOptions($month, $year){
		$nbOfDays = getNbOfDays($month ,$year);
		$options = "";
		for($i =1; $i <= $nbOfDays; $i++){
			$options .= '<option value="'. $i .'">'. $i . '</option>';
		}
		return $options;
	}

	public static function listGender($id = "0", $init = null){
		$html = "<select id='genderSelector".$id ."' name='genderSelector'>";
		$html .= '<option value="1">Homme</option>';
		$html .= '<option value="2">Femme</option>';
		$html .= "</select>";
		if($init){
			$html .='<script>
					$("#genderSelector'. $id .'").val('.$init.');
			</script>';
		}
		return $html;
	}

	private static function listDays($month, $year, $id = "0"){
		$html = "<select id='daySelector".$id ."' name='daySelector' class='days'>";
		$html .= self::getDaysOptions($month, $year) . "</select>";
		return $html;
	}

	public static function listWeekDays(){
		$days = DB::getInstance()->select('days','*',null,false);
		$html = "Jour:
				<select id='DaySelector' name='DaySelector'>";
		$options = "";
		foreach($days as $day){
			$options .= '<option value="'. $day['id'] .'">'. $day['title'] . '</option>';
		}
		$html .= $options . "</select>";
		return $html;
	}

	private static function getScript($id = "0", $init){
		$day = null;
		$month = null;
		$year = null;
		if($init){
			$timeStamp = strtotime($init);
			$day = date('d', $timeStamp);
			$month = date('m', $timeStamp);
			$year = date('Y', $timeStamp);
		}
	return '<script>
				console.log("on change created");
				$("#monthSelector'. $id .'").on("change", function() {
			        changeDays();
			    });
				$("#yearSelector'. $id .'").on("change", function() {
			        changeDays();
			    });

				function changeDays(){
					var month = $("#monthSelector'. $id .'").val();
					var year = $("#yearSelector'. $id .'").val();
					var selectedDay = $("#daySelector'. $id .'").val();
					callAjax("php/objects/getDays.php", {month: month, year:year}, "html", function (html) {
						tryToReselectAfterChange("#daySelector'. $id .'", html, selectedDay);
				        /*$("#daySelector'. $id .'").html(html).promise().done(function(){
			                while($("#daySelector'. $id .' option[value="+ selectedDay +"]").length == 0){
			                    selectedDay--;
			                }
			                $("#daySelector'. $id .'").val(selectedDay);
				        });*/
				    });
				}'.
				($day && $month && $year ? '
					$("#daySelector'. $id .'").val('.$day.');
				    $("#monthSelector'. $id .'").val('.$month.');
				    $("#yearSelector'. $id .'").val('.$year.');
				' : '')
				.'</script>';
	}

	private static function listMonths($id = "0"){
		$html = "<select id='monthSelector".$id ."' name='monthSelector' class='months'>";
		$html .= '<option value="1">Janvier</option>';
		$html .= '<option value="2">Février</option>';
		$html .= '<option value="3">Mars</option>';
		$html .= '<option value="4">Avril</option>';
		$html .= '<option value="5">Mai</option>';
		$html .= '<option value="6">Juin</option>';
		$html .= '<option value="7">Juillet</option>';
		$html .= '<option value="8">Août</option>';
		$html .= '<option value="9">Septembre</option>';
		$html .= '<option value="10">Octobre</option>';
		$html .= '<option value="11">Novembre</option>';
		$html .= '<option value="12">Décembre</option>';
		$html .= "</select>";
		return $html;
	}

	private static function listYears($id = "0"){
		$actualYear = intval(date('Y'));
		$html = "<select id='yearSelector".$id ."' name='yearSelector' class='years'>";
		$options = "";
		for($i = $actualYear-self::$minAge; $i >= $actualYear-self::$maxAge; $i--){
			$options .= '<option value="'. $i .'">'. $i . '</option>';
		}
		$html .= $options . "</select>";
		return $html;
	}

	public static function getGender($option){
		switch ($option){
			case 1:
				return 'Homme';
				break;
			case 2:
				return 'Femme';
				break;
		}
		fail();
		return null;
	}

	public static function tip($text){
		return '<div class="tip">'. $text .'</div>';
	}
	//TODO continuer le helper pour le carousel!
	//TODO Un array de divs
	public static function createCarousel($id){
		$html = '<div id="'. $id .'" class="carousel slide" data-ride="carousel">
					<!-- Indicators -->
					<ol class="carousel-indicators">';
		$html .= self::createCarouselIndicators($id, 10);
		$html .= '</ol>

					<!-- Wrapper for slides -->
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<img src="img_chania.jpg" alt="Chania">
						</div>

						<div class="item">
							<img src="img_chania2.jpg" alt="Chania">
						</div>

						<div class="item">
							<img src="img_flower.jpg" alt="Flower">
						</div>

						<div class="item">
							<img src="img_flower2.jpg" alt="Flower">
						</div>
					</div>

					<!-- Left and right controls -->
					<a class="left carousel-control" href="#{$id}" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Précédent</span>
					</a>
					<a class="right carousel-control" href="#{$id}" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Suivant</span>
					</a>
				</div>';
		return $html;
	}

	//TODO faire une function qui demande title, imgUrl, desc, pageUrl pour le carousel

	private static function createCarouselIndicators($id, $totalToCreate){
		$indicators = '';
		for($i = 0; $i < $totalToCreate; $i++){
			$indicators .= '<li data-target="#'.$id.'" data-slide-to="'. $i .'" ' . ($i == 0? 'class="active"' : '') .'></li>';
		}
		return $indicators;
	}
}