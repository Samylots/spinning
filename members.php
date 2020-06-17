<?php
/**
 * Created by PhpStorm.
 * User: Samuel
 * Date: 03/04/2016
 * Time: 18:38
 */

include( 'php/functions.php' );

$site = new Site();
$site->addModule('modules/purchases');
$site->addModule('modules/memberSpace');
$site->sidebar->addOption('Retour', 'index.php');
$site->toolbar->setTitle('Espace membre');
if($site->user->isLogged()){
	$site->sidebar->addOption('Mes infos', 'members.php');
	$site->sidebar->addOption('Mes achats', 'Purchases', 'loadPurchases');
	//$site->sidebar->addOption('Mes rabais', 'Discounts', 'showUserDiscounts');
	$site->sidebar->addOption('Déconnecter', null, 'logout');
	$site->start();

	$members = new Members();
	$member = $members->getOne($site->user->getUserId())->fetch();
	$html = '';
	$html .= '<div id="Member'. $member['id']  .'" class="Member item fix">';
	$html .= '<div id="MemberContent'. $member['id']  .'">';
	$html .=  $members->memberFormat($member);
	$html .= '</div>';
	$html .= '</div>';
	echo $html;
	?>
	<script>selectOption('infos');</script>
	<?php
	// TODO Faire la page pour les discount du membre seul
	// TODO Faire une page "mes cours" pour afficher juste les cours qu'il est inscrit!

	// TODO un module pour le coach pour avoir la liste des membre (nom du meeting + nom membre + age + etc... (une note si ya pas payé sa carte qu'il s'est inscrit avec!))

}else{
	$site->start();
	?>
	<button class="button edit" onclick="register()">S'inscrire</button>
	<button class="button edit" onclick="login()">Se connecter</button>
	<?php
}
$site->end();
?>