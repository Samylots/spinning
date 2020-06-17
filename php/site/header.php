<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 14:45
 */
if(file_exists('../functions.php')){
	include_once( '../functions.php' );
}
$headerMenu = new HeaderMenu();

if(hasPosted('url')){
	$headerMenu->setCurrentRequest(get('url'));
}

$user = new User();
$headerMenu->addOption('Accueil','index.php', null, $headerMenu->hasToBeActive(['index.php']));
$headerMenu->addOption('Horaire', 'index.php?m=Sessions',null, $headerMenu->hasToBeActive(['index.php?m=Sessions','schedules.php*']));

if($user->isLogged()){
	$headerMenu->addOption($user->getFullname(),'members.php', null, $headerMenu->hasToBeActive(['members.php*']));
	if($user->isAdmin()){
		$headerMenu->addOption('Administration','administration.php', null, $headerMenu->hasToBeActive(['administration*']));
	}
}else{
	$headerMenu->addOption('Espace membre','members.php', null, $headerMenu->hasToBeActive(['members.php*']));
}
?>
<div id="header" class="header fix"> <!--//ne pas utiliser la balise header, cela décale le body au complet...-->
	<div class="header_content">
		<div class="logo">
			<a class="" href="javascript: void(0)"><img src="img/site/logo.png" alt="Spinning de Beauce" onclick='redirect("index.php")' /></a>
		</div>
		<?php
		$headerMenu->getHeaderMenu();
		?>
	</div>
</div>