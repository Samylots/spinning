<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 11:12
 */
include_once( '../functions.php' );
if(hasPosted(['sessionId','packagesId','code'])){
	if(!get('memberId')){
		$user = new User();
		$memberId = $user->getUserId();
	}else{
		$memberId = get('memberId');
	}
	$purchases = new Purchases();
	$purchases->setMemberId($memberId);
	$purchases->buyCard(get('sessionId'),get('packagesId'),get('code'));
}else{
	fail();
}