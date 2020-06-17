<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:20
 */

include( 'php/functions.php' );
$sessionId = get('session');
if($sessionId){
	$site = new Site();
	$site->addModule('modules/sessionWeeks');
	$site->sidebar->addOption('Retour',"administrationSessions.php?session=". get('session'));
	$site->toolbar->setTitle('Administration à la semaine d\'une session');
	$sessionModule = new Sessions();
	for($i =1; $i <= $sessionModule->getTotalWeeks(get('session'));$i++ ){
			$site->sidebar->addOption('Semaine ' . $i, null, 'openWeek('. $i .')' );
	}
	$site->start();
	$week = (get('week') ? get('week') : $sessionModule->getActualWeek(get('session')));
	?>

	<?php
	$site->end('<script>
		loadSessionWeek('. $week .', function (html) {
			$.when(setContent(html)).done(toggleLoadingDown);
		});
	</script>');
}else{
	redirect('administration.php');
}
?>