<?php

App::uses('AppModel', 'Model');

/**
 * ActivityFeed Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class ActivityFeed extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'ActivityFeed';
}
