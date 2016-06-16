<?php

/**
 * User model class file
 *
 * @package UrateIt
 * @subpackage UrateIt.Models
 */
App::uses('Model', 'Model');


class Admin extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'Admin';


/**
 * get fields for comments
 * 
 * @return return conditions
 */
	public function getUsersFields() {
		return array('id', 'is_deleted', 'email', 'password', 'full_name', 'avatar');
	}
}
