<?php

App::uses('AppModel', 'Model');

/**
 * WallpaperCustomInvite Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class WallpaperCustomInvite extends AppModel {

    /**
     * Model Name
     *
     * @var string
     */
    public $name = 'WallpaperCustomInvite';

    /**
     * This function is to check if is deleted or not
     * @nikhil parmar
     */
    public function beforeFind($queryData) {
        //parent::beforeFind();
        $queryData['conditions']['WallpaperCustomInvite.is_deleted'] = 0;
        return $queryData;
    }
}
