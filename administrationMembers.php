<?php
/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 03/04/2016
 * Time: 18:38
 */

include( 'php/functions.php' );
$site = new Site();
$site->addModule('modules/memberSpace');
$site->addModule('modules/purchases');
$site->sidebar->addOption('Retour', 'administration.php?m=Members');
if($site->user->isAdmin() && get('member')){
	$members = new Members();
	$member = $members->getOne(get('member'))->fetch();
	if(!$member){
		redirect('administration.php?m=Members');
	}
	$site->toolbar->setTitle('Gestion du membre: ' . $members->getFullName($member));
	$site->sidebar->addOption('Achats membre', 'Purchases', 'loadMemberPurchases('. get('member') .')');
	$site->sidebar->addOption('Changer mot de passe', null, 'changePassword('. get('member') .')');
	//$site->sidebar->addOption('Rabais membre', 'Discounts', 'showUserDiscounts');
	$site->start();
	?>
	<p class="item">Ici vous pouvez avoir accès à toute l'information du compte d'un membre à l'aide du menu de gauche.<br><br>
	- «Achats membre» vous permet de consulter les achats que le membre a effectué chez <?= Config::$title ?>.<br><br>
	<?php

	//TODO faire le gestion des achats du membre (ajouter la fonction de linker un discount (même passé date et mettre à jour le prix dans la facture?))
	//TODO faire la gestion des discounts
}else{
	redirect('administration.php?m=Members');
}
$site->end();
?>