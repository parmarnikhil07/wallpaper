<?php

App::uses('AppModel', 'Model');

/**
 * WallpaperRating Model
 *
 */
class WallpaperRating extends AppModel {

    /**
     * Model Name
     *
     * @var string
     */
    public $name = 'WallpaperRating';

    /**
     * This function is to check if is deleted or not
     * @nikhil parmar
     */
    public function beforeFind($queryData) {
        //parent::beforeFind();
        $queryData['conditions']['WallpaperRating.is_deleted'] = 0;
        return $queryData;
    }
}
