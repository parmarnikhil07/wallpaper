<?php

App::uses('AppModel', 'Model');

/**
 * Wallpaper Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class Wallpaper extends AppModel {

    /**
     * Model Name
     *
     * @var string
     */
    public $name = 'Wallpaper';

    /**
     * This function is to check if is deleted or not
     */
    public function beforeFind($queryData) {
        //parent::beforeFind();
        $queryData['conditions']['Wallpaper.is_deleted'] = 0;
        return $queryData;
    }

}
