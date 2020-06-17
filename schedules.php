<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 19:20
 */

include( 'php/functions.php' );
if(get('session')){
	$site = new Site();
	$site->addModule('modules/registrations');
	$site->addModule('modules/sessionWeeks');
	$site->sidebar->addOption('Retour',"index.php?m=Sessions");
	$site->toolbar->setTitle('Horaire d\'une session');
	$site->start();
	$sessionModule = new Sessions();
	$session = get('session');
	if(get('week')){
		$week = get('week');
		if($week > $sessionModule->getActualWeek($session) +2 | $week < $sessionModule->getActualWeek($session)){
			$week = $sessionModule->getActualWeek($session);
		}
	}else{
		$week = $sessionModule->getActualWeek($session);
	}
	?>

	<?php
	$site->end('<script>
					loadPublicSessionWeek('. $week .', function (html) {
						$.when(setContent(html)).done(toggleLoadingDown);
					});
				</script>');
}else{
	redirect('index.php');
}
?>