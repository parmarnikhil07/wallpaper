<?php

App::uses('AppModel', 'Model');

/**
 * Dashboard Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class Dashboard extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'Dashboard';
}
