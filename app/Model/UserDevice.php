<?php

App::uses('AppModel', 'Model');

/**
 * UserDevice Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class UserDevice extends AppModel {

    /**
     * Model Name
     *
     * @var string
     */
    public $name = 'UserDevice';

    /**
     * This function is to check if is deleted or not
     * @nikhil parmar
     */
    public function beforeFind($queryData) {
        if (Configure::read('addBeforFindCondition') == true) {
            $queryData['conditions']['UserDevice.is_deleted'] = 0;
        } else {
            Configure::write('addBeforFindCondition', true);
        }
        return $queryData;
    }
}
