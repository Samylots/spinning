<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 14:28
 */

include ('php\functions.php');
$site = new Site();
$site->addModule('modules/sessions');
$site->sidebar->addOption('Accueil', 'index.php', null, true);
$site->sidebar->addOption('Sessions', 'Sessions', 'loadPublicSessions');
if(isCurrentRequest("index.php?m=Sessions")){
	$site->toolbar->setTitle('Horaire d\'une session');
}
$site->start();
?>
<!--Default Content goes here-->
<div class="item"> ceci est un exemple comme quoi du content par défaut peut être généré sans avoir nécessairement de module!</div>
<?php
$site->end();
?>
