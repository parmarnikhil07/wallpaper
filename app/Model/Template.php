<?php

App::uses('AppModel', 'Model');

/**
 * Template Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class Template extends AppModel {

/**
 * Model Name
 *
 * @var string
 */
	public $name = 'Template';
}
