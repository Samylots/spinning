<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-04-07
 * Time: 09:30
 */

include_once ('../functions.php');
$module = new Purchases();
echo $module->cardForm();
?>