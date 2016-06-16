<?php

/**
 * User model class file
 *
 * @package Geemode
 * @subpackage Geemode.Models
 */
App::uses('Model', 'Model');

/**
 * User model class.
 *
 * @package Geemode
 * @subpackage Geemode.Models
 */
class User extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'User';

/**
 * get fields for comments
 * 
 * @return return conditions
 */
	public function getUsersFields() {
		return array('id', 'user_name', 'email', 'password', 'user_key', 'allow_following_detail', 'full_name', 'avatar', 'cover_photo', 'user_name', 'user_time_zone', 'is_verified', 'is_deleted');
	}
        
        /**
	 * This function is to check if is deleted or not
	 * @nikhil parmar
	 */
	public function beforeFind($queryData) {
		if(Configure::read('addBeforFindCondition') == true){
			$queryData['conditions']['User.is_deleted'] = 0;
		} else {
			Configure::write('addBeforFindCondition',true);
		}
		return $queryData;
	}

}
