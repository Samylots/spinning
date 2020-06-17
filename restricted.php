<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-15
 * Time: 14:28
 */

include ('php\functions.php');
//redirect('administration.php');
$site = new Site();
$site->sidebar->addOption('Accueil', 'index.php');
$site->toolbar->setTitle('Restriction d\'une page');
$site->start();
?>  <div class="item">
	<div class="item"> Désolé, la page que vous tentez d'accéder vous est interdit.</div>
	<div class="item">Si vous croyer que c'est une erreur, veuillez communiquer avec l'administrateur du site web.</div>
	<div class="item">Merci de votre compréhension!</div></div>
	<script>
		toggleLoadingDown();
	</script>
<?php
$site->end();
?>
