<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-16
 * Time: 10:47
 */

include_once ('../functions.php');
if(hasPosted('q')){
	$module = new Members();
	$members = $module->getAllAutocomplete(get('q'));
	$foundMembers = [];
	while( $member = $members->fetch() ){
		$foundMembers[] = $member;
	}
	echo json_encode($foundMembers);
}else{
	echo json_encode(['error' => 'not found']);

}
?>