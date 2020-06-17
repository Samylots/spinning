<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2016-03-21
 * Time: 12:51
 */
interface ModuleFormatter{
	public function format($data);
	public function adminEditForm( $data);
	public function adminNewForm();
}