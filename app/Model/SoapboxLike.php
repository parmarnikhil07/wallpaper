<?php

App::uses('AppModel', 'Model');

/**
 * SoapboxLike Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class SoapboxLike extends AppModel {

    /**
     * Model Name
     *
     * @var string
     */
    public $name = 'SoapboxLike';

    /**
     * This function is to check if is deleted or not
     * @nikhil parmar
     */
    public function beforeFind($queryData) {
        //parent::beforeFind();
        $queryData['conditions']['SoapboxLike.is_deleted'] = 0;
        return $queryData;
    }
}
