<?php

App::uses('AppModel', 'Model');

/**
 * PollItem Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class PollItem extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'PollItem';
}
