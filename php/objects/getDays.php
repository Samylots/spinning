<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-31
 * Time: 08:24
 */
include_once( '../functions.php' );

if(hasPosted(['month', 'year'])){
	echo Helper::getDaysOptions(get('month'),get('year'));
}else{
	fail();
}