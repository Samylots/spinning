<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 09:30
 */

include_once ('../functions.php');
if(hasPosted(['packageId','selectedPeriods'])){
	$module = new Purchases();
	$module->canSelectPeriod(get('packageId'), json_decode(get('selectedPeriods')));
}
?>