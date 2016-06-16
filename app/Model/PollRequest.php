<?php

App::uses('AppModel', 'Model');

/**
 * PollRequest Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class PollRequest extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'PollRequest';
}
