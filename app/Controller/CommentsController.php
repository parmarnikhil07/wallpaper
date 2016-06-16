<?php

App::uses('AppController', 'Controller');

/**
 * Users controller
 *
 * @package    URateIt
 * @subpackage URateIt.Controllers
 */
class CommentsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     *
     */
    public $name = 'Comments';

    /**
     * Models name
     *
     * @var array
     */
    public $uses = array('WallpaperComment', 'Wallpaper', 'User', 'ActivityFeed', 'UserDevice', 'Template', 'Contact');

    public function comment_wallpaper($accessKey = null, $userkey = null) {
        $validationErrors = false;
        if (empty($this->request->data['wallpaper_id'])) {
            $this->apiErrors[] = 'Wallpaper id is missing.';
            $validationErrors = true;
        }
        if (empty($this->request->data['comment'])) {
            $this->apiErrors[] = 'Comment is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $wallpaper = $this->Wallpaper->find('first', array(
                'conditions' => array('Wallpaper.id' => $this->request->data['wallpaper_id']),
                'fields' => array('Wallpaper.id', 'Wallpaper.user_id', 'Wallpaper.title', 'Wallpaper.comments_count', 'Wallpaper.allow_comment', 'Wallpaper.privacy_type')
            ));
            if (!empty($wallpaper)) {
                if ($wallpaper['Wallpaper']['allow_comment']) {
                    $wallpaperData = array();
                    $wallpaperData['WallpaperComment']['wallpaper_id'] = $this->request->data['wallpaper_id'];
                    $wallpaperData['WallpaperComment']['comment'] = $this->request->data['comment'];
                    $wallpaperData['WallpaperComment']['user_id'] = $this->currentUser['id'];
                    $wallpaperData['WallpaperComment']['id'] = null;
                    $this->WallpaperComment->save($wallpaperData);
                    $wallpaper['Wallpaper']['comments_count'] = $wallpaper['Wallpaper']['comments_count'] + 1;
                    $this->Wallpaper->save($wallpaper);
                    $this->apiOutputArr['comments_count'] = $wallpaper['Wallpaper']['comments_count'];

                    /*                     * ***************      notification part       **************************** */
                    if ($this->currentUser['id'] != $wallpaper['Wallpaper']['user_id']) {
                        $is_allow_notification = $this->is_allow_notification($wallpaper['Wallpaper']['user_id'], 'comment');
                        if ($is_allow_notification) {
                            $getUserDevices = $this->UserDevice->find('all', array('fields' => array('UserDevice.device_token', 'UserDevice.device_type'), 'conditions' => array('UserDevice.user_id' => $wallpaper['Wallpaper']['user_id'], 'UserDevice.is_login' => 1)));
                            if (!empty($getUserDevices)) {
                                $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'wallpaper_comment_ntf', 'Template.status' => 1, 'Template.type' => 1), 'fields' => array('subject', 'content', 'tags')));
                                $arr1 = explode(',', $templateDetail['Template']['tags']);
                                $arr2 = array($this->currentUser['full_name'], $wallpaper['Wallpaper']['title']);
                                $msg = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                                $title = $wallpaper['Wallpaper']['title'];
                                try {
                                    foreach ($getUserDevices as $deviceToken) {
                                        //$badgeCount = $this->getBadgeCount($userContact['UserContact']['contact_id']);
                                        $badgeCount = 0;
                                        if (!empty($deviceToken['UserDevice']['device_token']) && $deviceToken['UserDevice']['device_type'] == 'i') {
                                            $this->sendIphoneNotification($deviceToken['UserDevice']['device_token'], $title, $msg, $this->request->data['wallpaper_id'], null, null);
                                        } else if (!empty($deviceToken['UserDevice']['device_token']) && $deviceToken['UserDevice']['device_type'] == 'a') {
                                            $this->sendAndroidNotification($deviceToken['UserDevice']['device_token'], $title, $msg, $this->request->data['wallpaper_id'], null, null);
                                        }
                                    }
                                } catch (Exception $e) {
                                    
                                }
                            }
                        }
                    }
                    /*                     * ***************      notification part    ***************** */

                    $activityDataArr = array();
                    $activityDataArr['ActivityFeed']['id'] = null;
                    $activityDataArr['ActivityFeed']['feed_user_id'] = $this->currentUser['id'];
                    $activityDataArr['ActivityFeed']['wallpaper_owner_user_id'] = $wallpaper['Wallpaper']['user_id'];
                    $activityDataArr['ActivityFeed']['wallpaper_id'] = $wallpaper['Wallpaper']['id'];
                    $activityDataArr['ActivityFeed']['wallpaper_title'] = $wallpaper['Wallpaper']['title'];
                    $activityDataArr['ActivityFeed']['action'] = ACTIVITY_FEED_COMMENT_SOAPBOX;
                    $activityDataArr['ActivityFeed']['privacy_type'] = $wallpaper['Wallpaper']['privacy_type'];
                    $this->saveActivityFeed($activityDataArr);
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiResponseCode = API_CODE_FAIL;
                    $this->errorsToReturn[] = 'Comment not allowed.';
                }
            } else {
                $this->apiResponseCode = API_CODE_FAIL;
                $this->errorsToReturn[] = 'Wallpaper not found.';
            }
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function get_comments($accessKey = null, $userKey = null, $wallpaper_id = null) {
        $validationErrors = false;
        if (empty($wallpaper_id)) {
            $this->apiErrors[] = 'Wallpaper id is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $WallpaperComments = $this->WallpaperComment->find('all', array(
                'conditions' => array('WallpaperComment.wallpaper_id' => $wallpaper_id),
                'fields' => array('User.full_name, User.avatar, User.user_name, User.show_full_name, Contact.id, WallpaperComment.comment, WallpaperComment.user_id', 'WallpaperComment.id as comment_id'),
                'joins' => array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.id = WallpaperComment.user_id'
                        )
                    ),
                    array(
                        'table' => 'contacts',
                        'alias' => 'Contact',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "OR" => array(
                                array('Contact.to_user_id' => 'WallpaperComment.user_id', 'Contact.from_user_id' => $this->currentUser['id']),
                                array('Contact.from_user_id' => 'WallpaperComment.user_id', 'Contact.to_user_id' => $this->currentUser['id'])
                            )
                        )
                    )
                )
            ));

            $WallpaperComments1 = array();
            if (!empty($WallpaperComments)) {
                foreach ($WallpaperComments as $arr) {
                    $Wallpaper['comment_id'] = $arr['WallpaperComment']['comment_id'];
                    $Wallpaper['comment'] = $arr['WallpaperComment']['comment'];
                    $Wallpaper['user_id'] = $arr['WallpaperComment']['user_id'];
                    if (empty($arr['User']['show_full_name']) && empty($arr['Contact']['id'])) {
                        $Wallpaper['full_name'] = $arr['User']['user_name'];
                    } else {
                        $Wallpaper['full_name'] = $arr['User']['full_name'];
                    }
                    $Wallpaper['user_name'] = $arr['User']['user_name'];
                    $Wallpaper['avatar'] = !empty($arr['User']['avatar']) ? $arr['User']['avatar'] : 'no_picture_icon.png';
                    $WallpaperComments1[] = $Wallpaper;
                }
            }
            $this->apiOutputArr['wallpaperComments'] = $WallpaperComments1;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

}
