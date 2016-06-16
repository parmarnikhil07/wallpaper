<?php

App::uses('AppModel', 'Model');

/**
 * WallpaperComment Model
 *
 */
class WallpaperComment extends AppModel {

    /**
     * Model Name
     *
     * @var string
     */
    public $name = 'WallpaperComment';

    /**
     * This function is to check if is deleted or not
     * @nikhil parmar
     */
    public function beforeFind($queryData) {
        //parent::beforeFind();
        $queryData['conditions']['WallpaperComment.is_deleted'] = 0;
        return $queryData;
    }
}
