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
	$site->addModule('modules/schedules');
	$site->addModule('modules/rates');
	$site->sidebar->addOption('Retour',"administration.php");
	$site->sidebar->addOption('Horaire', null, 'loadSchedules');
	$site->sidebar->addOption('Tarifs', null, 'loadRates');
	$site->sidebar->addOption('Semaines', 'administrationSessionWeeks.php?session='.get('session'));
	$site->toolbar->setTitle('Administration général d\'une session');
	$sessions = new Sessions();
	$session = $sessions->getOne($sessionId)->fetch();
	if(!$session){
		redirect('administration.php?m=Sessions');
	}
	$site->start();
	?>

	<?php
	$site->end('<script>
		loadSchedules(function(html){
			$.when(setContent(html)).done(toggleLoadingDown);
		});
	</script>');
}else{
	redirect('administration.php');
}
?>