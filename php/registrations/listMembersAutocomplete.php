<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-06
 * Time: 17:03
 */
include_once( '../functions.php' );
if(hasPosted('q')){
	$members= new Members();
	$membersWithPurchases = $members->getAllWithPurchasesAutocomplete(get('q'));
	$actualMembers = [];
	while($member = $membersWithPurchases->fetch()){
		$actualMembers[] = $member;
	}
	echo json_encode($actualMembers);
}else{
	fail();
}
?>