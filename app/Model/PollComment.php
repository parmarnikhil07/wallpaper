<?php

App::uses('AppModel', 'Model');

/**
 * PollComment Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class PollComment extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'PollComment';

/**
 * get fields for comments
 * 
 * @return return conditions
 */
	public function getCommentsFields() {
		return array('User.full_name, User.avatar, User.user_name, PollComment.comment, PollComment.user_id', 'PollComment.id');
	}

/**
 * get comments fields array
 * 
 * @param string $arr array of comments
 * @return return conditions
 */
	public function getCommentsFieldsArray($arr = array()) {
		$PollComments1['user_id'] = $arr['PollComment']['user_id'];
		$PollComments1['comment_id'] = $arr['PollComment']['id'];
		$PollComments1['comment'] = $arr['PollComment']['comment'];
		$PollComments1['full_name'] = $arr['User']['full_name'];
		$PollComments1['user_name'] = $arr['User']['user_name'];
		$PollComments1['avatar'] = $arr['User']['avatar'];
		return $PollComments1;
	}
}
