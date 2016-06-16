<?php

App::uses('AppModel', 'Model');

/**
 * UserSocialKey Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class UserSocialKey extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'UserSocialKey';
}
