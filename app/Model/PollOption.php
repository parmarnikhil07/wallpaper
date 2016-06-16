<?php

App::uses('AppModel', 'Model');

/**
 * PollOption Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class PollOption extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'PollOption';
}
