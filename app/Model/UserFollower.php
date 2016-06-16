<?php

App::uses('AppModel', 'Model');

/**
 * UserFollower Model
 *
 * @property Subscription $Subscription
 * @property ApiKey $ApiKey
 * @property AssociationFollower $AssociationFollower
 * @property EventFavorite $EventFavorite
 * @property JoinRequest $JoinRequest
 */
class UserFollower extends AppModel {

    /**
     * Model Name
     *
     * @var string
     */
    public $name = 'UserFollower';

    /**
     * This function is to check if is deleted or not
     * @nikhil parmar
     */
    public function beforeFind($queryData) {
        if (Configure::read('addBeforFindCondition') == true) {
            $queryData['conditions']['UserFollower.is_deleted'] = 0;
        } else {
            Configure::write('addBeforFindCondition', true);
        }
        return $queryData;
    }

}
