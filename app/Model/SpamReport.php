<?php

App::uses('AppModel', 'Model');

/**
 * Group Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class SpamReport extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'SpamReport';
}
