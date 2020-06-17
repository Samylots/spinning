<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 10:06
 */
include_once ('../functions.php');
$module = new Purchases();
echo $module->subscriptionForm();
?>