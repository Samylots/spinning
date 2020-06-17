<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-14
 * Time: 19:02
 */
include_once( '../functions.php' );
if(hasPosted('registrationId')){
	$meetings = new Meetings();
	$registrations = new Registrations();
	$registration = $registrations->getOne(get('registrationId'))->fetch();
	$meetingId = $registration['meeting_id'];
	$receiptId = $registration['subscription_receipt_id'];

	if($meetings->isStarted($meetingId)){
		alert('Désolé, il est impossible de changer les inscriptions d\'une séance en cours ou terminée!');
	}else if($meetings->isCancellationLimitPassed($meetingId, $receiptId) ){
		$registrations->Delete(get('registrationId'));
		$purchases = new Purchases();
		$purchase = $purchases->getOne($receiptId)->fetch();
		$timeLimit = $purchase['meetingCancelDeadline'];
		alert('Vous avez bien été désinscrit.<br> Parcontre, le temps limite pour se désinscrire est dépassé.
				De ce fait, il vous est impossible de récuprer cette séance pour vous inscrire à une autre séance.<br>
				Afin de pouvoir récuperer une séance, il est préférable de se désinscrire au moins ' . formatTime($timeLimit) .
				' avant le début de la séance. <br> Merci de votre compréhension!');
	}else{
		$registrations->deepDelete(get('registrationId'));
	}

}else{
	fail();
}
?>