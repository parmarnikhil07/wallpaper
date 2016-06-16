<?php
App::uses('AppController', 'Controller');

/**
 * Users controller
 *
 * @package    Wallpaper
 * @subpackage Wallpaper.Controllers
 */
class WallpapersController extends AppController {

    /**
     * Controller name
     *
     * @var string
     *
     */
    public $name = 'Wallpaper';

    /**
     * Models name
     *
     * @var array
     */
    public $uses = array('User', 'ApiKey', 'Wallpaper', 'Template', 'Contact', 'UserDevice', 'WallpaperComment', 'WallpaperLike', 'WallpaperRating', 'ActivityFeed', 'DefaultWallpaperSetting', 'UserFollower', 'WallpaperCustomInvite', 'WallpaperRating', 'Group', 'GroupUser');

    /**
     * Components name
     *
     * @var array
     */
    public $components = array('ImageUpload', 'MDImage');
    public $privacyPolicyArr = array(0, 1, 2, 3);
    public $ratingArr = array(0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5);

    /**
     * Constant Image compress high qulty per name
     *
     * @var array
     */
    const IMG_COMPRESS_HIGH_QLTY_PER = 50;

    /**
     * Constant Image compress low qulty per name
     *
     * @var array
     */
    const IMG_COMPRESS_LOW_QLTY_PER = 10;

    /**
     * API method to login a particular user and generate a user key
     * This method requires POST data.
     *
     * API URL: /Wallpaperes/create_Wallpaper/{ACCESS_KEY}/{USER_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function create_Wallpaper($accessKey = null, $userkey = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $validationErrors = false;
            if (!$validationErrors) {
                if (empty($this->request->data['title'])) {
                    $this->apiErrors[] = 'Title is missing.';
                    $validationErrors = true;
                }
                if (!isset($this->request->data['privacy_type'])) {
                    $this->apiErrors[] = 'Privacy type is missing.';
                    $validationErrors = true;
                } else if (!in_array($this->request->data['privacy_type'], $this->privacyPolicyArr)) {
                    $this->apiErrors[] = 'privacy_type value is not proper.';
                    $validationErrors = true;
                } else if ($this->request->data['privacy_type'] == 3) {
                    if (empty($this->request->data['user_json']) && empty($this->request->data['group_json'])) {
                        $this->apiErrors[] = 'If privacy_type=3, pass either user_json OR group_json.';
                        $validationErrors = true;
                    }
                }
                if (empty($this->request->data['title_font_face'])) {
                    $this->apiErrors[] = 'title_font_face is missing.';
                    $validationErrors = true;
                }
                if (empty($this->request->data['title_font_size'])) {
                    $this->apiErrors[] = 'title_font_face is missing.';
                    $validationErrors = true;
                }
                if (empty($this->request->data['title_font_color'])) {
                    $this->apiErrors[] = 'title_font_color is missing.';
                    $validationErrors = true;
                }
                if (empty($this->request->data['video_duration'])) {
                    $this->apiErrors[] = 'Video duration is missing.';
                    $validationErrors = true;
                }
                if (!$validationErrors) {
                    if (!empty($this->request->params['form']['thumb']) && $this->request->params['form']['thumb']['error'] == 0 && !empty($this->request->params['form']['thumb']['tmp_name'])) {
                        $this->request->data['thumb'] = self::uploadThumb($this->request->params['form']['thumb']);
                    } else {
                        $this->apiErrors[] = 'thumb is missing.';
                        $validationErrors = true;
                    }
                    if (!empty($this->request->params['form']['video']) && $this->request->params['form']['video']['error'] == 0 && !empty($this->request->params['form']['video']['tmp_name'])) {
                        $this->request->data['video'] = self::uploadVideo($this->request->params['form']['video']);
                    } else {
                        $this->apiErrors[] = 'video is missing.';
                        $validationErrors = true;
                    }
                }
            }
            if (!$validationErrors) {
                $this->Wallpaper->create();
                $this->request->data['user_id'] = $this->currentUser['id'];
                $this->Wallpaper->set($this->request->data);
                if ($this->Wallpaper->save($this->request->data)) {
                    if (!empty($this->request->params['form']['background_image'])) {
                        $updateBackgrounImage = array();
                        $backgroundImage = $this->request->params['form'];
                        if ($backgroundImage['background_image']['error'] == 0 && !empty($backgroundImage['background_image']['tmp_name'])) {
                            $uploadsDir = WWW_ROOT . "files/new_Wallpaperes";
                            $WallpaperBackgroundDir = WWW_ROOT . "files/new_Wallpaperes/backgrounds";
                            if (!is_dir($uploadsDir)) {
                                //mkdir($uploadsDir, true);
                            }
                            if (!is_dir($WallpaperBackgroundDir)) {
                                //mkdir($WallpaperBackgroundDir, true);
                            }
                            $name = $this->Common->generateRandomValueWithTime(40);
                            $randomName = str_replace(".", "", strtotime("now"));
                            $name = $randomName . md5($name);
                            $ext = $this->ImageUpload->getFileExtensionFromUploadedFileUsingMimeType($this->request->params['form']['background_image']['tmp_name']);
                            $backgroundHighImage = $name . '_high.' . $ext;
                            $backgroundLowImage = $name . '_low.' . $ext;
                            $tmpName = $backgroundImage['background_image']["tmp_name"];
                            $updateBackgrounImage['Wallpaper']['background_image'] = $backgroundHighImage;
                            $this->ImageUpload->compressImage($tmpName, $WallpaperBackgroundDir . "/" . $backgroundHighImage, self::IMG_COMPRESS_HIGH_QLTY_PER);
                            $this->ImageUpload->compressImage($WallpaperBackgroundDir . "/" . $backgroundHighImage, $WallpaperBackgroundDir . "/" . $backgroundLowImage, self::IMG_COMPRESS_LOW_QLTY_PER);
                        }
                        $updateBackgrounImage['Wallpaper']['id'] = $this->Wallpaper->id;
                        $this->Wallpaper->set($updateBackgrounImage);
                        $this->Wallpaper->save($updateBackgrounImage);
                    }
                    $this->request->data['notification_users_id_array'] = array();
                    if ($this->request->data['privacy_type'] == 3) {
                        if (!empty($this->request->data['user_json'])) {
                            $usersArr = json_decode($this->request->data['user_json'], true);
                            if (!empty($usersArr)) {
                                self::invite_users_array_for_Wallpaper($usersArr, $this->Wallpaper->id);
                            }
                        }
                        if (!empty($this->request->data['group_json'])) {
                            $groupArr = json_decode($this->request->data['group_json'], true);
                            if (!empty($groupArr)) {
                                self::invite_groups_array_for_Wallpaper($groupArr, $this->Wallpaper->id);
                            }
                        }
                    }

                    /*                     * *****************************      notification part       **************************** */
                    $notificationUserList = array();
                    if ($this->request->data['privacy_type'] == 0) {
                        $contactList = $this->getContactsUserIdListArr($this->currentUser['id']);
                        $followersList = $this->UserFollower->find('list', array("conditions" => array('UserFollower.following_id' => $this->currentUser['id']), "fields" => array("UserFollower.follower_id")));
                        $notificationUserList = $followersList + $contactList;
                        $notificationUserList = array_unique($notificationUserList);
                    } else if ($this->request->data['privacy_type'] == 1) {
                        $notificationUserList = $this->getContactsUserIdListArr($this->currentUser['id']);
                    } else if ($this->request->data['privacy_type'] == 2) {
                        $notificationUserList = $this->UserFollower->find('list', array("conditions" => array('UserFollower.following_id' => $this->currentUser['id']), "fields" => array("UserFollower.follower_id")));
                    } else if ($this->request->data['privacy_type'] == 3) {
                        $notificationUserList = $this->request->data['notification_users_id_array'];
                    }
                    if (!empty($notificationUserList) && ($this->request->data['privacy_type'] == 1 || $this->request->data['privacy_type'] == 3)) {
                        $getUserDevices = $this->User->find('all', array(
                            'fields' => array('UserDevice.device_token', 'UserDevice.user_id', 'UserDevice.device_type', 'User.full_name', 'User.email'),
                            'conditions' => array('UserDevice.user_id' => $notificationUserList, 'UserDevice.is_login' => 1, 'User.allow_notification' => 1),
                            'joins' => array(
                                array(
                                    'table' => 'user_devices',
                                    'alias' => 'UserDevice',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'User.id = UserDevice.user_id'
                                    )
                                )
                            )
                        ));
                        if (!empty($getUserDevices)) {
                            $emailTemplateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'Wallpaper_invite'),
                                'fields' => array('subject', 'content', 'tags')));
                            $emailArr1 = explode(',', $emailTemplateDetail['Template']['tags']);
                            $Wallpaper_id_code = $this->Wallpaper->id . "-" . $this->Wallpaper->id . "-" . $this->Wallpaper->id;
                            $schemaWallpaperLink = WEB_URL . 'Wallpaper_invite/' . $this->encrypt($Wallpaper_id_code, ENCRYPT_DECRYPT_KEY);
                            $WallpaperImage = WALP_THUMB_URL . $this->request->data['thumb'];
                            $WallpaperTitle = $this->request->data['title'];
                            $WallpaperTitleLink = "<a href='" . $schemaWallpaperLink . "'>" . $WallpaperTitle . "</a>";
                            $WallpaperImageLink = "<a href='" . $schemaWallpaperLink . "' style='text-align: center' ><img src='" . $WallpaperImage . "' title='" . $WallpaperTitle . "' width='200px'>";

                            $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'Wallpaper_create_ntf', 'Template.status' => 1, 'Template.type' => 1), 'fields' => array('subject', 'content', 'tags')));
                            $arr1 = explode(',', $templateDetail['Template']['tags']);
                            $arr2 = array($this->currentUser['full_name'], $this->request->data['title']);
                            $msg = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                            $title = $this->request->data['title'];
                            try {
                                foreach ($getUserDevices as $UserDetailArr) {
                                    //$badgeCount = $this->getBadgeCount($userContact['UserContact']['contact_id']);
                                    $is_allow_notification = $this->is_allow_notification($UserDetailArr['UserDevice']['user_id'], 'other');
                                    if ($is_allow_notification) {
                                        $badgeCount = 0;
                                        if (!empty($UserDetailArr['UserDevice']['device_token']) && $UserDetailArr['UserDevice']['device_type'] == 'i') {
                                            $this->sendIphoneNotification($UserDetailArr['UserDevice']['device_token'], $title, $msg, $this->Wallpaper->id, null, null);
                                        } else if (!empty($UserDetailArr['UserDevice']['device_token']) && $UserDetailArr['UserDevice']['device_type'] == 'a') {
                                            $this->sendAndroidNotification($UserDetailArr['UserDevice']['device_token'], $title, $msg, $this->Wallpaper->id, null, null);
                                        }
                                    }

                                    /*                                     * ********* email part ************************ */

                                    $emailArr2 = array($UserDetailArr['User']['full_name'], $this->currentUser['user_name'], $WallpaperTitleLink, $WallpaperImageLink);
                                    $emailData = str_replace($emailArr1, $emailArr2, $emailTemplateDetail['Template']['content']);
                                    if (strpos($UserDetailArr['User']['email'], 'example') == false) {
                                        $this->send_email($emailTemplateDetail['Template']['subject'], $emailData, $UserDetailArr['User']['email'], 'mail_temp');
                                    }
                                }
                            } catch (Exception $e) {
                                
                            }
                        }
                    }
                    /*                     * *********************      notification part    ********************** */
                    $activityDataArr = array();
                    $activityDataArr['ActivityFeed']['id'] = null;
                    $activityDataArr['ActivityFeed']['feed_user_id'] = $this->currentUser['id'];
                    $activityDataArr['ActivityFeed']['Wallpaper_owner_user_id'] = $this->currentUser['id'];
                    $activityDataArr['ActivityFeed']['Wallpaper_id'] = $this->Wallpaper->id;
                    $activityDataArr['ActivityFeed']['Wallpaper_title'] = $this->request->data['title'];
                    $activityDataArr['ActivityFeed']['action'] = ACTIVITY_FEED_CREATE_WALP;
                    $activityDataArr['ActivityFeed']['privacy_type'] = $this->request->data['privacy_type'];
                    $this->saveActivityFeed($activityDataArr);

                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                }
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_NO_POST;
        }
    }

    public function invite_users_array_for_Wallpaper($userArr = null, $Wallpaper_id = null) {
        foreach ($userArr as $userData) {
            if (!empty($userData['user_id'])) {
                $userData = $this->User->find('first', array('conditions' => array('id' => $userData['user_id']), 'fields' => array('id', 'email', 'full_name')));
                if (!empty($userData)) {
                    $inviteData['user_id'] = $userData['User']['id'];
                    $inviteData['Wallpaper_id'] = $Wallpaper_id;
                    $inviteData['email'] = $userData['User']['email'];
                    $inviteData['full_name'] = $userData['User']['full_name'];
                    $this->WallpaperCustomInvite->id = null;
                    $this->WallpaperCustomInvite->save($inviteData);
                    // for sending notification
                    array_push($this->request->data['notification_users_id_array'], $userData['User']['id']);
                }
            } else if (!empty($userData['email']) && empty($userData['user_id'])) {
                $inviteData['user_id'] = 0;
                $inviteData['Wallpaper_id'] = $Wallpaper_id;
                $inviteData['email'] = isset($userData['email']) ? $userData['email'] : "";
                $inviteData['full_name'] = isset($userData['full_name']) ? $userData['full_name'] : "";
                $this->WallpaperCustomInvite->id = null;
                $this->WallpaperCustomInvite->save($inviteData);
            }
        }
    }

    public function invite_groups_array_for_Wallpaper($groupArr = null, $Wallpaper_id = null) {
        foreach ($groupArr as $groupId) {
            $groupUserData = $this->Group->find('all', array(
                'conditions' => array('Group.id' => $groupId),
                'fields' => array('GroupUser.group_id', 'GroupUser.user_id', 'GroupUser.email', 'GroupUser.full_name'),
                'joins' => array(
                    array(
                        'table' => 'group_users',
                        'alias' => 'GroupUser',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Group.id = GroupUser.group_id'
                        )
                    )
                )
            ));
            if (!empty($groupUserData)) {
                foreach ($groupUserData as $groupUser) {
                    if (!empty($groupUser['GroupUser']['email'])) {
                        $inviteData['user_id'] = $groupUser['GroupUser']['user_id'];
                        $inviteData['group_id'] = $groupId;
                        $inviteData['Wallpaper_id'] = $Wallpaper_id;
                        $inviteData['email'] = $groupUser['GroupUser']['email'];
                        $inviteData['full_name'] = $groupUser['GroupUser']['full_name'];
                        $this->WallpaperCustomInvite->id = null;
                        $this->WallpaperCustomInvite->save($inviteData);
                        array_push($this->request->data['notification_users_id_array'], $groupUser['GroupUser']['user_id']);
                    }
                }
            }
        }
    }

    public function get_custom_invited_data($accessKey = null, $userkey = null, $Wallpaper_id = null) {
        $validationErrors = false;
        if (!isset($Wallpaper_id)) {
            $this->apiErrors[] = 'Wallpaper_id is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $WallpaperData = $this->Wallpaper->find('first', array(
                'conditions' => array('id' => $Wallpaper_id),
                'fields' => array('Wallpaper.privacy_type')
            ));
            if (!empty($WallpaperData)) {
                if ($WallpaperData['Wallpaper']['privacy_type'] == 3) {
                    $WallpaperInviteData = $this->WallpaperCustomInvite->find('all', array(
                        'conditions' => array('Wallpaper_id' => $Wallpaper_id),
                        'fields' => array('group_id', 'user_id', 'email', 'full_name')
                    ));
                    $invitedArr = array();
                    $inviteGroupData = array();
                    $isGroup = false;
                    if (!empty($WallpaperInviteData)) {
                        foreach ($WallpaperInviteData as $WallpaperInviteData) {
                            if (!empty($WallpaperInviteData['WallpaperCustomInvite']['group_id'])) {
                                $inviteGroupData[] = $WallpaperInviteData['WallpaperCustomInvite']['group_id'];
                                $isGroup = true;
                            } else {
                                unset($WallpaperInviteData['WallpaperCustomInvite']['group_id']);
                                $invitedArr[] = $WallpaperInviteData['WallpaperCustomInvite'];
                            }
                        }
                    }
                    if ($isGroup) {
                        $this->apiOutputArr['invitedDataType'] = "groups";
                        $this->apiOutputArr['invitedData'] = array_values(array_unique($inviteGroupData));
                    } else {
                        $this->apiOutputArr['invitedDataType'] = "users";
                        $this->apiOutputArr['invitedData'] = $invitedArr;
                    }
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiErrors[] = 'Wallpaper invite type mismatch.';
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                }
            } else {
                $this->errorsToReturn[] = 'Wallpaper not found.';
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    public function uploadThumb($thumbFile = null) {
        try {
            $uploadsDir = WWW_ROOT . "files/new_Wallpaperes";
            $WallpaperThumbDir = WWW_ROOT . "files/new_Wallpaperes/thumbs";
            if (!is_dir($uploadsDir)) {
                //mkdir($uploadsDir, true);
            }
            if (!is_dir($WallpaperThumbDir)) {
                //mkdir($WallpaperThumbDir, true);
            }
            $name = $this->Common->generateRandomValueWithTime(40);
            $randomName = str_replace(".", "", strtotime("now"));
            $name = $randomName . md5($name);
            $ext = $this->ImageUpload->getFileExtensionFromUploadedFileUsingMimeType($thumbFile['tmp_name']);
            if (empty($ext) || empty($name)) {
                $this->apiErrors[] = 'Extension or name issue.';
                $validationErrors = true;
            } else {
                $thumbImage = $name . "." . $ext;
                $thumbUploaded = move_uploaded_file($thumbFile['tmp_name'], $WallpaperThumbDir . "/" . $thumbImage);
                if (!$thumbUploaded) {
                    $this->apiErrors[] = 'Fail to upload thumb.';
                    $validationErrors = true;
                } else {
                    return $thumbImage;
                }
            }
        } catch (Exception $exc) {
            $this->apiErrors[] = 'something went wrong to upload thumb.';
            $validationErrors = true;
        }
    }

    public function uploadVideo($videoFile = null) {
        try {
            $uploadsDir = WWW_ROOT . "files/new_Wallpaperes";
            $WallpaperVideoDir = WWW_ROOT . "files/new_Wallpaperes/videos";
            if (!is_dir($uploadsDir)) {
                //mkdir($uploadsDir, true);
            }
            if (!is_dir($WallpaperVideoDir)) {
                //mkdir($WallpaperVideoDir, true);
            }
            $name = $this->Common->generateRandomValueWithTime(40);
            $randomName = str_replace(".", "", strtotime("now"));
            $name = $randomName . md5($name);
            $ext = $this->ImageUpload->getFileExtensionFromUploadedVideoUsingMimeType($videoFile['type']);
            if (empty($ext) || empty($name)) {
                $this->apiErrors[] = 'Extension or name issue.';
                $validationErrors = true;
            } else {
                $videoName = $name . "." . $ext;
                $thumbUploaded = move_uploaded_file($videoFile['tmp_name'], $WallpaperVideoDir . "/" . $videoName);
                if (!$thumbUploaded) {
                    $this->apiErrors[] = 'Fail to upload video.';
                    $validationErrors = true;
                } else {
                    return $videoName;
                }
            }
        } catch (Exception $exc) {
            $this->apiErrors[] = 'something went wrong to upload thumb.';
            $validationErrors = true;
        }
        return false;
    }

    public function edit_Wallpaper($accessKey = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $validationErrors = false;
            if (!isset($this->request->data['Wallpaper_id'])) {
                $this->apiErrors[] = 'Wallpaper_id is missing.';
                $validationErrors = true;
            }
            if (!isset($this->request->data['allow_rating'])) {
                $this->apiErrors[] = 'allow_rating is missing.';
                $validationErrors = true;
            }
            if (!isset($this->request->data['allow_comment'])) {
                $this->apiErrors[] = 'allow_comment is missing.';
                $validationErrors = true;
            }
            if (isset($this->request->data['privacy_type'])) {
                if (!in_array($this->request->data['privacy_type'], $this->privacyPolicyArr)) {
                    $this->apiErrors[] = 'privacy_type value is not proper.';
                    $validationErrors = true;
                } else if ($this->request->data['privacy_type'] == 3) {
                    if (empty($this->request->data['user_json']) && empty($this->request->data['group_json'])) {
                        $this->apiErrors[] = 'If privacy_type=3, pass either user_json OR group_json.';
                        $validationErrors = true;
                    }
                }
            }
            if (!$validationErrors) {
                $WallpaperDetails = $this->Wallpaper->find('first', array('conditions' => array('Wallpaper.id' => $this->request->data['Wallpaper_id']),
                    'fields' => array('id', 'user_id', 'privacy_type')));
                if (!empty($WallpaperDetails)) {
                    if ($WallpaperDetails['Wallpaper']['user_id'] == $this->currentUser['id']) {
                        if (isset($this->request->data['privacy_type'])) {
                            if ($this->request->data['privacy_type'] == 3) {
                                /*                                 * ******** delete all records  ****************** */
                                $this->WallpaperCustomInvite->deleteAll(
                                        array('WallpaperCustomInvite.Wallpaper_id' => $WallpaperDetails['Wallpaper']['id'])
                                        , false
                                );

                                /*                                 * ********     add all records back    ****************** */
                                if (!empty($this->request->data['user_json'])) {
                                    $usersArr = json_decode($this->request->data['user_json'], true);
                                    if (!empty($usersArr)) {
                                        self::invite_users_array_for_Wallpaper($usersArr, $this->request->data['Wallpaper_id']);
                                    }
                                }
                                if (!empty($this->request->data['group_json'])) {
                                    $groupArr = json_decode($this->request->data['group_json'], true);
                                    if (!empty($groupArr)) {
                                        self::invite_groups_array_for_Wallpaper($groupArr, $this->request->data['Wallpaper_id']);
                                    }
                                }
                            }
                        }
                        $this->Wallpaper->id = $this->request->data['Wallpaper_id'];
                        $this->Wallpaper->set($this->request->data);
                        if ($this->Wallpaper->save($this->request->data)) {
                            $this->apiResponseCode = API_CODE_SUCCESS;
                        } else {
                            $this->errorsToReturn[] = 'Fail to update Wallpaper.';
                            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                        }
                    } else {
                        $this->errorsToReturn[] = 'You can not edit this Wallpaper.';
                        $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                    }
                } else {
                    $this->errorsToReturn[] = 'Wallpaper not found.';
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                }
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_NO_POST;
        }
    }

    public function get_Wallpaper_detail($accessKey = null, $userkey = null, $Wallpaper_id = null) {
        if (!empty($Wallpaper_id)) {
            $myWallpapers = array();
            $myWallpapersData = array();
            $this->Wallpaper->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'fields' => array('full_name', 'user_name', 'avatar')
                    )
                )
                    ), false);
            $WallpaperArr = $this->Wallpaper->find("first", array(
                'conditions' => array('Wallpaper.id' => $Wallpaper_id),
                'fields' => array(
                    'Wallpaper.id as Wallpaper_id',
                    'Wallpaper.user_id',
                    'User.full_name',
                    'User.user_name',
                    'User.avatar',
                    'Wallpaper.title',
                    'Wallpaper.theme_type',
                    'Wallpaper.description',
                    'Wallpaper.video',
                    'Wallpaper.thumb',
                    'Wallpaper.video_duration',
                    'Wallpaper.tags',
                    'Wallpaper.privacy_type',
                    'Wallpaper.allow_comment',
                    'Wallpaper.allow_rating',
                    'Wallpaper.likes_count',
                    'Wallpaper.comments_count',
                    'Wallpaper.avg_rating',
                    'Wallpaper.rating_count',
                    'Wallpaper.background_color',
                    'Wallpaper.background_image',
                    'Wallpaper.title_font_face',
                    'Wallpaper.title_font_size',
                    'Wallpaper.title_font_color',
                    'Wallpaper.created'),
            ));
            if (!empty($WallpaperArr)) {
                $is_liked = $this->WallpaperLike->find('first', array(
                    'conditions' => array(
                        'WallpaperLike.user_id' => $this->currentUser['id'],
                        'WallpaperLike.Wallpaper_id' => $WallpaperArr['Wallpaper']['Wallpaper_id']
                    )
                ));
                if (!empty($is_liked)) {
                    $WallpaperArr['Wallpaper']['is_liked'] = true;
                } else {
                    $WallpaperArr['Wallpaper']['is_liked'] = false;
                }

                $is_rated = $this->WallpaperRating->find('first', array(
                    'conditions' => array(
                        'WallpaperRating.user_id' => $this->currentUser['id'],
                        'WallpaperRating.Wallpaper_id' => $WallpaperArr['Wallpaper']['Wallpaper_id']
                    )
                ));
                if (!empty($is_rated)) {
                    $WallpaperArr['Wallpaper']['is_rated'] = true;
                    $WallpaperArr['Wallpaper']['rating'] = $is_rated['WallpaperRating']['rating'];
                } else {
                    $WallpaperArr['Wallpaper']['is_rated'] = false;
                }
                $WallpaperArr['Wallpaper']['commentCount'] = $this->WallpaperComment->find('count', array("conditions" => array('WallpaperComment.Wallpaper_id' => $Wallpaper_id)));
                $WallpaperArr['Wallpaper'] = array_merge($WallpaperArr['User'], $WallpaperArr['Wallpaper']);
                $this->apiOutputArr['Wallpaper'] = $WallpaperArr['Wallpaper'];
                $this->apiResponseCode = API_CODE_SUCCESS;
            } else {
                $this->apiErrors[] = 'Soapboox not found.';
                $this->apiResponseCode = API_CODE_FAIL;
            }
        } else {
            $this->apiErrors[] = 'Soapboox id is missing.';
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function get_my_Wallpaper_list($accessKey = null, $userkey = null, $page = null) {
        if (!empty($page)) {
            $myWallpapers = array();
            $myWallpapersData = array();

            $this->Wallpaper->bindModel(array(
                'hasOne' => array(
                    'WallpaperCustomInvite' => array(
                        'className' => 'WallpaperCustomInvite',
                        'foreignKey' => 'Wallpaper_id',
                        'fields' => array('user_id', 'Wallpaper_id')
                    ),
                    'WallpaperLike' => array(
                        'className' => 'WallpaperLike',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperLike.user_id' => $this->currentUser['id']),
                        'fields' => array('user_id')
                    ),
                    'WallpaperRating' => array(
                        'className' => 'WallpaperRating',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperRating.user_id' => $this->currentUser['id']),
                        'fields' => array('rating')
                    )
                )), false
            );
            $this->paginate = array(
                'conditions' => array('Wallpaper.user_id' => $this->currentUser['id']),
                'fields' => array('Wallpaper.id as Wallpaper_id', 'Wallpaper.title', 'Wallpaper.description', 'Wallpaper.thumb', 'Wallpaper.video_duration', 'Wallpaper.tags', 'Wallpaper.privacy_type', 'Wallpaper.allow_comment', 'Wallpaper.allow_rating', 'Wallpaper.likes_count', 'Wallpaper.comments_count', 'Wallpaper.avg_rating', 'Wallpaper.title_font_face', 'Wallpaper.title_font_size', 'Wallpaper.title_font_color', 'Wallpaper.created', 'WallpaperLike.user_id', 'WallpaperRating.rating'),
                'order' => array('Wallpaper.created' => 'DESC'),
                'group' => array('Wallpaper.id'),
                'limit' => NORMAL_PAGINATION_LIMIT,
                'page' => $page
            );
            $myWallpapers = $this->paginate('Wallpaper');
            if (!empty($myWallpapers)) {
                foreach ($myWallpapers as $myWallpaper) {
                    $isLiked['is_liked'] = false;
                    $isRated['is_rated'] = false;
                    $isRated['rating'] = '';
                    if (!empty($myWallpaper['WallpaperLike']['user_id'])) {
                        $isLiked['is_liked'] = true;
                    }
                    $mergedArr = array_merge($myWallpaper['Wallpaper'], $isLiked);
                    if (!empty($myWallpaper['WallpaperRating']['rating'])) {
                        $isRated['is_rated'] = true;
                        $isRated['rating'] = $myWallpaper['WallpaperRating']['rating'];
                    }
                    $mergedArr = array_merge($mergedArr, $isRated);
                    $myWallpapersData[] = $mergedArr;
                }
                /*                 * ***** check if next page exist or not ********* */
                $has_next = $this->request->params['paging']['Wallpaper']['nextPage'];
                $this->apiOutputArr['has_next'] = $has_next;
                /*                 * ***** check next page ends ********* */
            }
            $this->apiOutputArr['myWallpaper'] = $myWallpapersData;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiErrors[] = 'Page number is missing.';
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function today_past_Wallpaper_list($accessKey = null, $userkey = null, $user_id = null, $page = null) {
        $validationErrors = false;
        if (empty($user_id)) {
            $this->apiErrors[] = 'User id is missing.';
            $validationErrors = true;
        }
        if (empty($page)) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $userData = $this->User->find("first", array("conditions" => array("User.id" => $user_id), "fields" => array("id", "profile_visibility")));
            if (!empty($userData)) {
                $showList = false;
                if ($userData['User']['profile_visibility'] == "PUBLIC") {
                    $showList = true;
                } else {
                    if ($this->isFriend(null, $user_id)) {
                        $showList = true;
                    }
                }
                $todayWallpaperData = array();
                $WallpapersData = array();
                if ($showList) {
                    $myWallpapers = array();
                    $followers = $this->getFollowersListArr($this->currentUser['id']);
                    $contactsUserIdArr = $this->getContactsUserIdListArr($this->currentUser['id']);
                    $this->Wallpaper->bindModel(array(
                        'belongsTo' => array(
                            'User' => array(
                                'className' => 'User',
                                'foreignKey' => 'user_id',
                                'fields' => array('full_name', 'avatar', 'user_name', 'show_full_name')
                            )
                        ),
                        'hasOne' => array(
                            'WallpaperCustomInvite' => array(
                                'className' => 'WallpaperCustomInvite',
                                'foreignKey' => 'Wallpaper_id',
                                'fields' => array('user_id', 'Wallpaper_id')
                            ),
                            'WallpaperLike' => array(
                                'className' => 'WallpaperLike',
                                'foreignKey' => 'Wallpaper_id',
                                'conditions' => array('WallpaperLike.user_id' => $this->currentUser['id']),
                                'fields' => array('user_id')
                            ),
                            'WallpaperRating' => array(
                                'className' => 'WallpaperRating',
                                'foreignKey' => 'Wallpaper_id',
                                'conditions' => array('WallpaperRating.user_id' => $this->currentUser['id']),
                                'fields' => array('rating')
                            )
                        )
                            ), false
                    );
                    $this->paginate = array(
                        'conditions' => array(
                            'OR' => array(
                                'Wallpaper.privacy_type' => 0,
                                array('Wallpaper.privacy_type' => 1, 'Wallpaper.user_id' => $contactsUserIdArr),
                                array('Wallpaper.privacy_type' => 2, 'Wallpaper.user_id' => $followers),
                                array('Wallpaper.privacy_type' => 3, 'WallpaperCustomInvite.user_id' => $this->currentUser['id'])
                            ),
                            'Wallpaper.user_id' => $user_id,
                        //                        'DATE(Wallpaper.created)' => date('Y-m-d'),   //condition for today
                        ),
                        'fields' => array('Wallpaper.id as Wallpaper_id', 'User.id as user_id', 'User.full_name as full_name', 'User.show_full_name as show_full_name', 'User.user_name as user_name', 'User.avatar as avatar', 'Wallpaper.title', 'Wallpaper.description', 'Wallpaper.video_duration', 'Wallpaper.thumb', 'Wallpaper.tags', 'Wallpaper.privacy_type', 'Wallpaper.allow_comment', 'Wallpaper.allow_rating', 'Wallpaper.likes_count', 'Wallpaper.comments_count', 'Wallpaper.avg_rating', 'Wallpaper.title_font_face', 'Wallpaper.title_font_size', 'Wallpaper.title_font_color', 'Wallpaper.created', 'WallpaperLike.user_id', 'WallpaperRating.rating', 'Contact.id'),
                        'order' => array('Wallpaper.created' => 'DESC'),
                        'joins' => array(
                            array(
                                'table' => 'contacts',
                                'alias' => 'Contact',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    "OR" => array(
                                        array('Contact.to_user_id' => 'Wallpaper.user_id', 'Contact.from_user_id' => $this->currentUser['id']),
                                        array('Contact.from_user_id' => 'Wallpaper.user_id', 'Contact.to_user_id' => $this->currentUser['id'])
                                    )
                                )
                            )
                        ),
                        'group' => array('Wallpaper.id'),
                        'limit' => NORMAL_PAGINATION_LIMIT,
                        'page' => $page
                    );

                    $myWallpapers = $this->paginate('Wallpaper');
                    $istodayWallpaper = false;
                    if (!empty($myWallpapers)) {
                        foreach ($myWallpapers as $myWallpaper) {
                            $isLiked['is_liked'] = false;
                            $isRated['is_rated'] = false;
                            $isRated['rating'] = '';
                            if (empty($myWallpaper['Contact']['id']) && empty($myWallpaper['User']['show_full_name'])) {
                                $myWallpaper['User']['full_name'] = $myWallpaper['User']['user_name'];
                            }
                            $mergedArr = array_merge($myWallpaper['User'], $myWallpaper['Wallpaper']);
                            if (!empty($myWallpaper['WallpaperLike']['user_id'])) {
                                $isLiked['is_liked'] = true;
                            }
                            $mergedArr = array_merge($mergedArr, $isLiked);
                            if (!empty($myWallpaper['WallpaperRating']['rating'])) {
                                $isRated['is_rated'] = true;
                                $isRated['rating'] = $myWallpaper['WallpaperRating']['rating'];
                            }
                            $mergedArr = array_merge($mergedArr, $isRated);
                            $currentDate = date('Y-m-d');
                            $dat = date('Y-m-d', strtotime($mergedArr['created']));
                            if ($dat == $currentDate) {
                                $todayWallpaperData[] = $mergedArr;
                                $istodayWallpaper = true;
                            } else {
                                $WallpapersData[] = $mergedArr;
                            }
                        }
                        /*                         * ***** check if next page exist or not ********* */
                        $has_next = $this->request->params['paging']['Wallpaper']['nextPage'];
                        $this->apiOutputArr['has_next'] = $has_next;
                        $this->apiOutputArr['is_today_Wallpaper_exist'] = $istodayWallpaper;
                        /*                         * ***** check next page ends ********* */
                    }
                }
                $this->apiOutputArr['TodayWallpaper'] = $todayWallpaperData;
                $this->apiOutputArr['Wallpaper'] = $WallpapersData;
                $this->apiResponseCode = API_CODE_SUCCESS;
            } else {
                $this->apiErrors[] = 'User not found.';
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function todays_new_Wallpaperes_list($accessKey = null, $userkey = null, $user_id = null, $page = null) {
        $validationErrors = false;
        if (empty($user_id)) {
            $this->apiErrors[] = 'User id is missing.';
            $validationErrors = true;
        }
        if (empty($page)) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $myWallpapers = array();
            $followers = $this->getFollowersListArr($user_id);
            $contactsUserIdArr = $this->getContactsUserIdListArr($user_id);
            $todayWallpaperData = array();
            $this->Wallpaper->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'fields' => array('full_name', 'avatar', 'user_name', 'show_full_name')
                    )
                ),
                'hasOne' => array(
                    'WallpaperCustomInvite' => array(
                        'className' => 'WallpaperCustomInvite',
                        'foreignKey' => 'Wallpaper_id',
                        'fields' => array('user_id', 'Wallpaper_id')
                    ),
                    'WallpaperLike' => array(
                        'className' => 'WallpaperLike',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperLike.user_id' => $user_id),
                        'fields' => array('user_id')
                    ),
                    'WallpaperRating' => array(
                        'className' => 'WallpaperRating',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperRating.user_id' => $user_id),
                        'fields' => array('rating')
                    )
                )
                    ), false
            );
            $this->paginate = array(
                'conditions' => array(
                    'OR' => array(
                        'Wallpaper.privacy_type' => 0,
                        array('Wallpaper.privacy_type' => 1, 'Wallpaper.user_id' => $contactsUserIdArr),
                        array('Wallpaper.privacy_type' => 2, 'Wallpaper.user_id' => $followers),
                        array('Wallpaper.privacy_type' => 3, 'WallpaperCustomInvite.user_id' => $user_id),
                        'Wallpaper.user_id' => $user_id,
                    ),
                    'DATE(Wallpaper.created)' => date('Y-m-d'), //condition for today
                ),
                'fields' => array('Wallpaper.id as Wallpaper_id', 'User.id as user_id', 'User.full_name as full_name', 'User.user_name as user_name', 'User.avatar as avatar', 'Wallpaper.title', 'Wallpaper.description', 'Wallpaper.thumb', 'Wallpaper.video', 'Wallpaper.video_duration', 'Wallpaper.tags', 'Wallpaper.privacy_type', 'Wallpaper.allow_comment', 'Wallpaper.allow_rating', 'Wallpaper.likes_count', 'Wallpaper.comments_count', 'Wallpaper.avg_rating', 'Wallpaper.title_font_face', 'Wallpaper.title_font_size', 'Wallpaper.title_font_color', 'Wallpaper.created', 'WallpaperLike.user_id', 'WallpaperRating.rating'),
                'joins' => array(
                    array(
                        'table' => 'contacts',
                        'alias' => 'Contact',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "OR" => array(
                                array('Contact.to_user_id' => 'Wallpaper.user_id', 'Contact.from_user_id' => $this->currentUser['id']),
                                array('Contact.from_user_id' => 'Wallpaper.user_id', 'Contact.to_user_id' => $this->currentUser['id'])
                            )
                        )
                    )
                ),
                'group' => array('Wallpaper.id'),
                'order' => array('Wallpaper.created' => 'DESC'),
                'limit' => NORMAL_PAGINATION_LIMIT,
                'page' => $page
            );

            $myWallpapers = $this->paginate('Wallpaper');
            if (!empty($myWallpapers)) {
                foreach ($myWallpapers as $myWallpaper) {
                    $isLiked['is_liked'] = false;
                    $isRated['is_rated'] = false;
                    $isRated['rating'] = '';
                    if (empty($myWallpaper['Contact']['id']) && empty($myWallpaper['User']['show_full_name'])) {
                        $myWallpaper['User']['full_name'] = $myWallpaper['User']['user_name'];
                    }
                    $mergedArr = array_merge($myWallpaper['User'], $myWallpaper['Wallpaper']);
                    if (!empty($myWallpaper['WallpaperLike']['user_id'])) {
                        $isLiked['is_liked'] = true;
                    }
                    $mergedArr = array_merge($mergedArr, $isLiked);
                    if (!empty($myWallpaper['WallpaperRating']['rating'])) {
                        $isRated['is_rated'] = true;
                        $isRated['rating'] = $myWallpaper['WallpaperRating']['rating'];
                    }
                    $mergedArr = array_merge($mergedArr, $isRated);
                    $todayWallpaperData[] = $mergedArr;
                }
                /*                 * ***** check if next page exist or not ********* */
                $has_next = $this->request->params['paging']['Wallpaper']['nextPage'];
                $this->apiOutputArr['has_next'] = $has_next;
                /*                 * ***** check next page ends ********* */
            }
            $this->apiOutputArr['todayWallpaper'] = $todayWallpaperData;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function search_Wallpaper($accessKey = null, $userkey = null) {
        $validationErrors = false;
        if (empty($this->request->data['search_text'])) {
            $this->apiErrors[] = 'Search text is missing.';
            $validationErrors = true;
        }
        if (empty($this->request->data['page'])) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $myWallpapers = array();
            $followers = $this->getFollowersListArr($this->currentUser['id']);
            $contactsUserIdArr = $this->getContactsUserIdListArr($this->currentUser['id']);
            $finalWallpaperData = array();
            $this->Wallpaper->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'fields' => array('full_name', 'user_name', 'avatar')
                    )
                ),
                'hasOne' => array(
                    'WallpaperCustomInvite' => array(
                        'className' => 'WallpaperCustomInvite',
                        'foreignKey' => 'Wallpaper_id',
                        'fields' => array('user_id', 'Wallpaper_id')
                    ),
                    'WallpaperLike' => array(
                        'className' => 'WallpaperLike',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperLike.user_id' => $this->currentUser['id']),
                        'fields' => array('user_id')
                    ),
                    'WallpaperRating' => array(
                        'className' => 'WallpaperRating',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperRating.user_id' => $this->currentUser['id']),
                        'fields' => array('rating')
                    )
                )
                    ), false
            );
            $this->paginate = array(
                'conditions' => array(
                    array(
                        'OR' => array(
                            'Wallpaper.privacy_type' => 0,
                            array('Wallpaper.privacy_type' => 1, 'Wallpaper.user_id' => $contactsUserIdArr),
                            array('Wallpaper.privacy_type' => 2, 'Wallpaper.user_id' => $followers),
                            array('Wallpaper.privacy_type' => 3, 'WallpaperCustomInvite.user_id' => $this->currentUser['id']),
                            'Wallpaper.user_id' => $this->currentUser['id']
                        )
                    ),
                    array(
                        'OR' => array(
                            'Wallpaper.title LIKE ' => '%' . $this->request->data['search_text'] . '%',
                            'Wallpaper.tags LIKE ' => '%' . $this->request->data['search_text'] . '%'
                        )
                    )
                ),
                'fields' => array('Wallpaper.id as Wallpaper_id', 'User.id as user_id', 'User.full_name as full_name', 'User.show_full_name as show_full_name', 'User.user_name as user_name', 'User.avatar as avatar', 'Wallpaper.title', 'Wallpaper.description', 'Wallpaper.video_duration', 'Wallpaper.thumb', 'Wallpaper.tags', 'Wallpaper.privacy_type', 'Wallpaper.allow_comment', 'Wallpaper.allow_rating', 'Wallpaper.likes_count', 'Wallpaper.comments_count', 'Wallpaper.avg_rating', 'Wallpaper.title_font_face', 'Wallpaper.title_font_size', 'Wallpaper.title_font_color', 'Wallpaper.created', 'WallpaperLike.user_id', 'WallpaperRating.rating', 'Contact.id'),
                'order' => array('Wallpaper.created' => 'DESC'),
                'joins' => array(
                    array(
                        'table' => 'contacts',
                        'alias' => 'Contact',
                        'type' => 'LEFT',
                        'conditions' => array(
                            "OR" => array(
                                array('Contact.to_user_id' => 'Wallpaper.user_id', 'Contact.from_user_id' => $this->currentUser['id']),
                                array('Contact.from_user_id' => 'Wallpaper.user_id', 'Contact.to_user_id' => $this->currentUser['id'])
                            )
                        )
                    )
                ),
                'limit' => NORMAL_PAGINATION_LIMIT,
                'group' => array('Wallpaper.id'),
                'page' => $this->request->data['page']
            );

            $wallpapers = $this->paginate('Wallpaper');
            if (!empty($wallpapers)) {
                foreach ($wallpapers as $wallpaper) {
                    $isLiked['is_liked'] = false;
                    $isRated['is_rated'] = false;
                    $isRated['rating'] = '';
                    if (empty($wallpaper['Contact']['id']) && empty($wallpaper['User']['show_full_name'])) {
                        $wallpaper['User']['full_name'] = $wallpaper['User']['user_name'];
                    }
                    $mergedArr = array_merge($wallpaper['User'], $wallpaper['Wallpaper']);
                    if (!empty($wallpaper['WallpaperLike']['user_id'])) {
                        $isLiked['is_liked'] = true;
                    }
                    $mergedArr = array_merge($mergedArr, $isLiked);
                    if (!empty($wallpaper['WallpaperRating']['rating'])) {
                        $isRated['is_rated'] = true;
                        $isRated['rating'] = $wallpaper['WallpaperRating']['rating'];
                    }
                    $mergedArr = array_merge($mergedArr, $isRated);
                    $finalWallpaperData[] = $mergedArr;
                }
            }
            /*             * ***** check if next page exist or not ********* */
            $has_next = $this->request->params['paging']['Wallpaper']['nextPage'];
            $this->apiOutputArr['has_next'] = $has_next;
            /*             * ***** check next page ends ********* */
            $this->apiOutputArr['Wallpaperes'] = $finalWallpaperData;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function like_Wallpaper($accessKey = null, $userkey = null) {
        $validationErrors = false;
        if (empty($this->request->data['Wallpaper_id'])) {
            $this->apiErrors[] = 'Wallpaper id is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $wallpaper = $this->Wallpaper->find('first', array(
                'conditions' => array('Wallpaper.id' => $this->request->data['Wallpaper_id']),
                'fields' => array('Wallpaper.id', 'Wallpaper.user_id', 'Wallpaper.likes_count', 'Wallpaper.title', 'Wallpaper.privacy_type')
            ));
            if (!empty($wallpaper)) {
                $wallpaperLiked = $this->WallpaperLike->find('first', array(
                    'conditions' => array('WallpaperLike.Wallpaper_id' => $this->request->data['Wallpaper_id'], 'WallpaperLike.user_id' => $this->currentUser['id']),
                    'fields' => 'WallpaperLike.id'
                ));
                if (empty($wallpaperLiked)) {
                    $wallpaperData = array();
                    $wallpaperData['WallpaperLike']['Wallpaper_id'] = $this->request->data['Wallpaper_id'];
                    $wallpaperData['WallpaperLike']['user_id'] = $this->currentUser['id'];
                    $wallpaperData['WallpaperLike']['id'] = null;
                    $this->WallpaperLike->save($wallpaperData);
                    $wallpaper['Wallpaper']['likes_count'] = $wallpaper['Wallpaper']['likes_count'] + 1;
                    $this->Wallpaper->save($wallpaper);
                    $this->apiOutputArr['likes_count'] = $wallpaper['Wallpaper']['likes_count'];

                    /*                     * ***************      notification part      **************************** */
                    if ($this->currentUser['id'] != $wallpaper['Wallpaper']['user_id']) {
                        $is_allow_notification = $this->is_allow_notification($wallpaper['Wallpaper']['user_id'], 'like');
                        if ($is_allow_notification) {
                            $getUserDevices = $this->UserDevice->find('all', array('fields' => array('UserDevice.device_token', 'UserDevice.device_type'), 'conditions' => array('UserDevice.user_id' => $wallpaper['Wallpaper']['user_id'], 'UserDevice.is_login' => 1)));
                            if (!empty($getUserDevices)) {
                                $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'Wallpaper_like_ntf', 'Template.status' => 1, 'Template.type' => 1), 'fields' => array('subject', 'content', 'tags')));
                                $arr1 = explode(',', $templateDetail['Template']['tags']);
                                $arr2 = array($this->currentUser['full_name'], $wallpaper['Wallpaper']['title']);
                                $msg = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                                $title = $wallpaper['Wallpaper']['title'];
                                try {
                                    foreach ($getUserDevices as $deviceToken) {
                                        //$badgeCount = $this->getBadgeCount($userContact['UserContact']['contact_id']);
                                        $badgeCount = 0;
                                        if (!empty($deviceToken['UserDevice']['device_token']) && $deviceToken['UserDevice']['device_type'] == 'i') {
                                            $this->sendIphoneNotification($deviceToken['UserDevice']['device_token'], $title, $msg, $this->request->data['Wallpaper_id'], null, null);
                                        } else if (!empty($deviceToken['UserDevice']['device_token']) && $deviceToken['UserDevice']['device_type'] == 'a') {
                                            $this->sendAndroidNotification($deviceToken['UserDevice']['device_token'], $title, $msg, $this->request->data['Wallpaper_id'], null, null);
                                        }
                                    }
                                } catch (Exception $e) {
                                    
                                }
                            }
                        }
                    }
                    /*                     * ***************      notification part      ***************** */
                    $activityDataArr = array();
                    $activityDataArr['ActivityFeed']['id'] = null;
                    $activityDataArr['ActivityFeed']['feed_user_id'] = $this->currentUser['id'];
                    $activityDataArr['ActivityFeed']['Wallpaper_owner_user_id'] = $wallpaper['Wallpaper']['user_id'];
                    $activityDataArr['ActivityFeed']['Wallpaper_id'] = $this->request->data['Wallpaper_id'];
                    $activityDataArr['ActivityFeed']['Wallpaper_title'] = $wallpaper['Wallpaper']['title'];
                    $activityDataArr['ActivityFeed']['action'] = ACTIVITY_FEED_LIKE_WALP;
                    $activityDataArr['ActivityFeed']['privacy_type'] = $wallpaper['Wallpaper']['privacy_type'];
                    $this->saveActivityFeed($activityDataArr);
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->ActivityFeed->deleteAll(
                            array(
                                'ActivityFeed.feed_user_id' => $this->currentUser['id'],
                                'ActivityFeed.Wallpaper_owner_user_id' => $wallpaper['Wallpaper']['user_id'],
                                'ActivityFeed.action' => ACTIVITY_FEED_LIKE_WALP,
                                'ActivityFeed.Wallpaper_id' => $this->request->data['Wallpaper_id']
                            )
                    );
                    $this->WallpaperLike->delete($wallpaperLiked['WallpaperLike']['id']);
                    $wallpaper['Wallpaper']['likes_count'] = $wallpaper['Wallpaper']['likes_count'] - 1;
                    $this->Wallpaper->save($wallpaper);
                    $this->apiOutputArr['likes_count'] = $wallpaper['Wallpaper']['likes_count'];
                    $this->apiResponseCode = API_CODE_SUCCESS;
                }
            } else {
                $this->apiResponseCode = API_CODE_FAIL;
                $this->errorsToReturn[] = 'Wallpaper not found.';
            }
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function rating_Wallpaper($accessKey = null, $userkey = null) {
        $validationErrors = false;
        if (empty($this->request->data['Wallpaper_id'])) {
            $this->apiErrors[] = 'Wallpaper id is missing.';
            $validationErrors = true;
        }
        if (empty($this->request->data['rating'])) {
            $this->apiErrors[] = 'Rating value is missing.';
            $validationErrors = true;
        } else if (!in_array($this->request->data['rating'], $this->ratingArr)) {
            $this->apiErrors[] = 'Rating value is not proper.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $wallpaper = $this->Wallpaper->find('first', array(
                'conditions' => array('Wallpaper.id' => $this->request->data['Wallpaper_id']),
                'fields' => array('Wallpaper.id', 'Wallpaper.user_id', 'Wallpaper.allow_rating', 'Wallpaper.rating_count', 'Wallpaper.total_rating_sum', 'Wallpaper.avg_rating', 'Wallpaper.privacy_type', 'Wallpaper.title')
            ));
            if (!empty($wallpaper)) {
                if ($wallpaper['Wallpaper']['allow_rating']) {
                    $ratedArr = $this->WallpaperRating->find('first', array(
                        'conditions' => array(
                            'WallpaperRating.user_id' => $this->currentUser['id'],
                            'WallpaperRating.Wallpaper_id' => $this->request->data['Wallpaper_id']
                        ),
                        'fields' => array('WallpaperRating.rating', 'WallpaperRating.id')
                    ));
                    if (empty($ratedArr)) {
                        $wallpaperData = array();
                        $newRatingSum = $wallpaper['Wallpaper']['total_rating_sum'] + $this->request->data['rating'];
                        $newRatingCount = $wallpaper['Wallpaper']['rating_count'] + 1;
                        $newAvgRating = $newRatingSum / $newRatingCount;
                        $wallpaperData['WallpaperRating']['Wallpaper_id'] = $this->request->data['Wallpaper_id'];
                        $wallpaperData['WallpaperRating']['rating'] = $this->request->data['rating'];
                        $wallpaperData['WallpaperRating']['user_id'] = $this->currentUser['id'];
                        $wallpaperData['WallpaperRating']['id'] = null;
                        $this->WallpaperRating->save($wallpaperData);
                        $newWallpaper['Wallpaper']['rating_count'] = $newRatingCount;
                        $newWallpaper['Wallpaper']['total_rating_sum'] = $newRatingSum;
                        $newAvgRating = $this->AvgRatingRoundUp($newAvgRating);
                        $newWallpaper['Wallpaper']['avg_rating'] = $newAvgRating;
                        $newWallpaper['Wallpaper']['id'] = $wallpaper['Wallpaper']['id'];
                        $result = $this->Wallpaper->save($newWallpaper);
                        if ($result) {
                            $this->apiOutputArr['Wallpaper']['rating_count'] = $newWallpaper['Wallpaper']['rating_count'];
                            $this->apiOutputArr['Wallpaper']['avg_rating'] = $newWallpaper['Wallpaper']['avg_rating'];
                            $this->apiOutputArr['Wallpaper']['id'] = $newWallpaper['Wallpaper']['id'];
                            $this->apiResponseCode = API_CODE_SUCCESS;
                            $activityDataArr = array();
                            $activityDataArr['ActivityFeed']['id'] = null;
                            $activityDataArr['ActivityFeed']['feed_user_id'] = $this->currentUser['id'];
                            $activityDataArr['ActivityFeed']['Wallpaper_owner_user_id'] = $this->currentUser['id'];
                            $activityDataArr['ActivityFeed']['Wallpaper_id'] = $this->request->data['Wallpaper_id'];
                            $activityDataArr['ActivityFeed']['Wallpaper_title'] = $wallpaper['Wallpaper']['title'];
                            $activityDataArr['ActivityFeed']['action'] = ACTIVITY_FEED_RATE_WALP;
                            $activityDataArr['ActivityFeed']['privacy_type'] = $wallpaper['Wallpaper']['privacy_type'];
                            $this->saveActivityFeed($activityDataArr);
                            /*                             * ***************      notification part      **************************** */
                            if ($this->currentUser['id'] != $wallpaper['Wallpaper']['user_id']) {
                                $is_allow_notification = $this->is_allow_notification($wallpaper['Wallpaper']['user_id'], 'other');
                                if ($is_allow_notification) {
                                    $getUserDevices = $this->UserDevice->find('all', array('fields' => array('UserDevice.device_token', 'UserDevice.device_type'), 'conditions' => array('UserDevice.user_id' => $wallpaper['Wallpaper']['user_id'], 'UserDevice.is_login' => 1)));
                                    if (!empty($getUserDevices)) {
                                        $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'Wallpaper_rating_ntf', 'Template.status' => 1, 'Template.type' => 1), 'fields' => array('subject', 'content', 'tags')));
                                        $arr1 = explode(',', $templateDetail['Template']['tags']);
                                        $arr2 = array($this->currentUser['full_name'], $wallpaper['Wallpaper']['title']);
                                        $msg = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                                        $title = $wallpaper['Wallpaper']['title'];
                                        try {
                                            foreach ($getUserDevices as $deviceToken) {
                                                //$badgeCount = $this->getBadgeCount($userContact['UserContact']['contact_id']);
                                                $badgeCount = 0;
                                                if (!empty($deviceToken['UserDevice']['device_token']) && $deviceToken['UserDevice']['device_type'] == 'i') {
                                                    $this->sendIphoneNotification($deviceToken['UserDevice']['device_token'], $title, $msg, $this->request->data['Wallpaper_id'], null, null);
                                                } else if (!empty($deviceToken['UserDevice']['device_token']) && $deviceToken['UserDevice']['device_type'] == 'a') {
                                                    $this->sendAndroidNotification($deviceToken['UserDevice']['device_token'], $title, $msg, $this->request->data['Wallpaper_id'], null, null);
                                                }
                                            }
                                        } catch (Exception $e) {
                                            
                                        }
                                    }
                                }
                            }
                            /*                             * ***************      notification part      ***************** */
                        } else {
                            $this->apiResponseCode = API_CODE_FAIL;
                            $this->errorsToReturn[] = 'Fail to update.';
                        }
                    } else {
                        $wallpaperData = array();
                        $newRatingSum = $wallpaper['Wallpaper']['total_rating_sum'] - $ratedArr['WallpaperRating']['rating'] + $this->request->data['rating'];
                        $odlRatingCount = $wallpaper['Wallpaper']['rating_count'];
                        $newAvgRating = $newRatingSum / $odlRatingCount;
                        $wallpaperData['WallpaperRating']['Wallpaper_id'] = $this->request->data['Wallpaper_id'];
                        $wallpaperData['WallpaperRating']['rating'] = $this->request->data['rating'];
                        $wallpaperData['WallpaperRating']['user_id'] = $this->currentUser['id'];
                        $wallpaperData['WallpaperRating']['id'] = $ratedArr['WallpaperRating']['id'];
                        $this->WallpaperRating->save($wallpaperData);
                        $newWallpaper['Wallpaper']['rating_count'] = $odlRatingCount;
                        $newWallpaper['Wallpaper']['total_rating_sum'] = $newRatingSum;
                        $newAvgRating = $this->AvgRatingRoundUp($newAvgRating);
                        $newWallpaper['Wallpaper']['avg_rating'] = $newAvgRating;
                        $newWallpaper['Wallpaper']['id'] = $wallpaper['Wallpaper']['id'];
                        $result = $this->Wallpaper->save($newWallpaper);
                        $this->apiOutputArr['Wallpaper']['rating_count'] = $newWallpaper['Wallpaper']['rating_count'];
                        $this->apiOutputArr['Wallpaper']['avg_rating'] = $newWallpaper['Wallpaper']['avg_rating'];
                        $this->apiOutputArr['Wallpaper']['id'] = $newWallpaper['Wallpaper']['id'];
                        $this->apiResponseCode = API_CODE_SUCCESS;
                    }
                } else {
                    $this->apiResponseCode = API_CODE_FAIL;
                    $this->errorsToReturn[] = 'Rating not allowed.';
                }
            } else {
                $this->apiResponseCode = API_CODE_FAIL;
                $this->errorsToReturn[] = 'Wallpaper not found.';
            }
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function trending_interest_list($accessKey = null, $userkey = null) {
        $this->Wallpaper->bindModel(array(
            'belongsTo' => array(
                'Interest' => array(
                    'className' => 'Interest',
                    'foreignKey' => 'interest_id',
                    'fields' => array('id', 'image', 'name')
                )
            )
                ), false
        );
        $interestArr = $this->Wallpaper->find('all', array(
            "conditions" => array('Wallpaper.interest_id NOT' => 0),
            "group" => array("Wallpaper.interest_id"),
            "order" => array("count(Wallpaper.id)" => "desc"),
            "fields" => array("Interest.id", "Interest.name", "Interest.image", "count(Wallpaper.id)"),
            "limit" => LESS_PAGINATION_LIMIT
                )
        );
        $interestList = array();
        foreach ($interestArr as $interestArr) {
            $interestList[] = $interestArr['Interest'];
        }
        $this->apiOutputArr['trendsInterestList'] = $interestList;
        $this->apiResponseCode = API_CODE_SUCCESS;
    }

    /* will return Wallpaperes based on interest id passed
     * 
     * @nikhil parmar
     */

    public function trending_interest_Wallpaper_list($accessKey = null, $userkey = null, $interest_id = null, $page = null) {
        $validationErrors = false;
        if (empty($interest_id)) {
            $this->apiErrors[] = 'Interest id is missing.';
            $validationErrors = true;
        }
        if (empty($page)) {
            $this->apiErrors[] = 'page is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $followers = $this->getFollowersListArr($this->currentUser['id']);
            $contactsUserIdArr = $this->getContactsUserIdListArr($this->currentUser['id']);
            $this->Wallpaper->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'fields' => array('full_name', 'user_name', 'avatar')
                    )
                ),
                'hasOne' => array(
                    'WallpaperCustomInvite' => array(
                        'className' => 'WallpaperCustomInvite',
                        'foreignKey' => 'Wallpaper_id',
                        'fields' => array('user_id', 'Wallpaper_id')
                    ),
                    'WallpaperLike' => array(
                        'className' => 'WallpaperLike',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperLike.user_id' => $this->currentUser['id']),
                        'fields' => array('user_id')
                    ),
                    'WallpaperRating' => array(
                        'className' => 'WallpaperRating',
                        'foreignKey' => 'Wallpaper_id',
                        'conditions' => array('WallpaperRating.user_id' => $this->currentUser['id']),
                        'fields' => array('rating')
                    )
                )
                    ), false
            );
            $curentDate = date('Y-m-d');
            $this->paginate = array(
                'conditions' => array(
                    'Wallpaper.interest_id' => $interest_id,
                    'Wallpaper.created >' => date('Y-m-d', strtotime($curentDate . ' - 2 days')),
                    'OR' => array(
                        'Wallpaper.privacy_type' => 0,
                        array('Wallpaper.privacy_type' => 1, 'Wallpaper.user_id' => $contactsUserIdArr),
                        array('Wallpaper.privacy_type' => 2, 'Wallpaper.user_id' => $followers),
                        array('Wallpaper.privacy_type' => 3, 'WallpaperCustomInvite.user_id' => $this->currentUser['id'])
                    )
                ),
                'fields' => array('Wallpaper.id as Wallpaper_id', 'User.id as user_id', 'User.full_name as full_name', 'User.user_name as user_name', 'User.avatar as avatar', 'Wallpaper.title', 'Wallpaper.description', 'Wallpaper.thumb', 'Wallpaper.video_duration', 'Wallpaper.tags', 'Wallpaper.privacy_type', 'Wallpaper.allow_comment', 'Wallpaper.allow_rating', 'Wallpaper.likes_count', 'Wallpaper.comments_count', 'Wallpaper.avg_rating', 'Wallpaper.title_font_face', 'Wallpaper.title_font_size', 'Wallpaper.title_font_color', 'Wallpaper.created', 'WallpaperLike.user_id', 'WallpaperRating.rating'),
                'order' => array('count(Wallpaper.like)' => 'DESC'),
                'limit' => 1,
                'page' => $page
            );

            $mostTrendingWallpaper = $this->paginate('Wallpaper');
            $mostTrendingWallpaperData = array();
            if (!empty($mostTrendingWallpaper)) {
                foreach ($mostTrendingWallpaper as $Wallpaper) {
                    $isLiked['is_liked'] = false;
                    $isRated['is_rated'] = false;
                    $mergedArr = array_merge($Wallpaper['User'], $Wallpaper['Wallpaper']);
                    if (!empty($Wallpaper['WallpaperLike']['user_id'])) {
                        $isLiked['is_liked'] = true;
                    }
                    $mergedArr = array_merge($mergedArr, $isLiked);
                    if (!empty($Wallpaper['WallpaperRating']['rating'])) {
                        $isRated['is_rated'] = true;
                        $isRated['rating'] = $Wallpaper['WallpaperRating']['rating'];
                    }
                    $mergedArr = array_merge($mergedArr, $isRated);
                    $mostTrendingWallpaperData[] = $mergedArr;
                }
            }
            if ($page == 1) {
                $this->apiOutputArr['mostTrendingWallpaper'] = $mostTrendingWallpaperData;
            }
            $trendingCondition = array(
                'Wallpaper.interest_id' => $interest_id,
                'OR' => array(
                    'Wallpaper.privacy_type' => 0,
                    array('Wallpaper.privacy_type' => 1, 'Wallpaper.user_id' => $contactsUserIdArr),
                    array('Wallpaper.privacy_type' => 2, 'Wallpaper.user_id' => $followers),
                    array('Wallpaper.privacy_type' => 3, 'WallpaperCustomInvite.user_id' => $this->currentUser['id'])
                )
            );
            if (!empty($mostTrendingWallpaperData)) {
                // overwrite the condition to escape the mostTrendingWallpaper
                $trendingCondition = array(
                    'Wallpaper.interest_id' => $interest_id,
                    'Wallpaper.id NOT' => $mostTrendingWallpaperData[0]['Wallpaper_id'],
                    'OR' => array(
                        'Wallpaper.privacy_type' => 0,
                        array('Wallpaper.privacy_type' => 1, 'Wallpaper.user_id' => $contactsUserIdArr),
                        array('Wallpaper.privacy_type' => 2, 'Wallpaper.user_id' => $followers),
                        array('Wallpaper.privacy_type' => 3, 'WallpaperCustomInvite.user_id' => $this->currentUser['id'])
                    )
                );
            }
            $this->paginate = array(
                'conditions' => $trendingCondition,
                'fields' => array('Wallpaper.id as Wallpaper_id', 'User.id as user_id', 'User.full_name as full_name', 'User.user_name as user_name', 'User.avatar as avatar', 'Wallpaper.title', 'Wallpaper.description', 'Wallpaper.thumb', 'Wallpaper.video_duration', 'Wallpaper.tags', 'Wallpaper.privacy_type', 'Wallpaper.allow_comment', 'Wallpaper.allow_rating', 'Wallpaper.likes_count', 'Wallpaper.comments_count', 'Wallpaper.avg_rating', 'Wallpaper.title_font_face', 'Wallpaper.title_font_size', 'Wallpaper.title_font_color', 'Wallpaper.created', 'WallpaperLike.user_id', 'WallpaperRating.rating'),
                'order' => array('count(Wallpaper.like)' => 'DESC'),
                'limit' => LESS_PAGINATION_LIMIT,
                'page' => $page
            );
            $Wallpapers = $this->paginate('Wallpaper');
            $trendingWallpaper = array();
            if (!empty($Wallpapers)) {
                foreach ($Wallpapers as $Wallpaper) {
                    $isLiked['is_liked'] = false;
                    $isRated['is_rated'] = false;
                    unset($isRated['rating']);
                    $mergedArr = array_merge($Wallpaper['User'], $Wallpaper['Wallpaper']);
                    if (!empty($Wallpaper['WallpaperLike']['user_id'])) {
                        $isLiked['is_liked'] = true;
                    }
                    $mergedArr = array_merge($mergedArr, $isLiked);
                    if (!empty($Wallpaper['WallpaperRating']['rating'])) {
                        $isRated['is_rated'] = true;
                        $isRated['rating'] = $Wallpaper['WallpaperRating']['rating'];
                    }
                    $mergedArr = array_merge($mergedArr, $isRated);
                    $trendingWallpaper[] = $mergedArr;
                }
            }
            /*             * ***** check if next page exist or not ********* */
            $has_next = $this->request->params['paging']['Wallpaper']['nextPage'];
            $this->apiOutputArr['has_next'] = $has_next;
            $this->apiOutputArr['interestWallpaper'] = $trendingWallpaper;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function delete_Wallpaper($accessKey = null, $userKey = null, $Wallpaper_id = null) {
        $validationErrors = false;
        if (empty($Wallpaper_id)) {
            $this->apiErrors[] = 'Wallpaper id is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $Wallpaper = $this->Wallpaper->find("first", array("conditions" => array("Wallpaper.id" => $Wallpaper_id), 'fields' => array('Wallpaper.id', 'Wallpaper.user_id')));
            if (!empty($Wallpaper)) {
                if ($Wallpaper['Wallpaper']['user_id'] == $this->currentUser['id']) {
                    $this->Wallpaper->delete($Wallpaper['Wallpaper']['id']);
                    $this->WallpaperComment->deleteAll(array("WallpaperComment.Wallpaper_id" => $Wallpaper['Wallpaper']['id']), false);
                    $this->WallpaperLike->deleteAll(array("WallpaperLike.Wallpaper_id" => $Wallpaper['Wallpaper']['id']), false);
                    $this->WallpaperRating->deleteAll(array("WallpaperRating.Wallpaper_id" => $Wallpaper['Wallpaper']['id']), false);
                    $this->ActivityFeed->deleteAll(array("ActivityFeed.Wallpaper_id" => $Wallpaper['Wallpaper']['id']), false);
                    $this->WallpaperCustomInvite->deleteAll(array("WallpaperCustomInvite.Wallpaper_id" => $Wallpaper['Wallpaper']['id']), false);
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiErrors[] = 'You cannot delete this Wallpaper.';
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                }
            } else {
                $this->apiErrors[] = 'Wallpaper not found.';
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    /**
     * add to friend
     *
     * API URL: /Wallpaper_invite/{code}
     *
     * @param string $code 
     *
     * @return open app if mobile else redirect to different URLs.
     */
    public function Wallpaper_invite_schema($code = null) {
        if (!empty($code)) {
            $code = $this->decrypt($code, ENCRYPT_DECRYPT_KEY);
            $Wallpaper_id = explode('-', $code);
            $device = $this->deviceType();
            if ($device == 'android') {
                ?>
                <script type="text/javascript">
                    //<![CDATA[
                    setTimeout(function () {
                        window.location = "https://play.google.com/store/apps/details?id=bluefusion.Wallpaper.socialrating";

                    }, 25);
                    window.location = "bluefusion.Wallpaper.socialrating://?Wallpaper_id=<?php echo $Wallpaper_id[0]; ?>";
                    //]]>
                </script>
                <?php
            } else if ($device == 'ios') {
                ?>
                <script type="text/javascript">
                    //<![CDATA[
                    setTimeout(function () {
                        window.location = 'https://itunes.apple.com';
                    }, 25);
                    window.location = 'Wallpaper://?Wallpaper_id=<?php echo $Wallpaper_id[0]; ?>';
                    //]]>
                </script>
                <?php
            } else {
                ?>
                <script type="text/javascript">
                    window.location = 'http://google.com';
                </script>
                <?php
            }
        } else {
            ?>
            <script type="text/javascript">
                window.location = 'http://google.com';
            </script>
            <?php
        }
    }

    /**
     * Index action
     *
     * Lists all Wallpaperes on admin side
     *
     * @return void
     */
    public function index() {
        try {
            if (!empty($this->request->params['named']['user_id'])) {
                $this->paginate = array('conditions' => array('Wallpaper.user_id' => $this->request->params['named']['user_id']), 'limit' => PAGE_LIMIT, 'order' => array('created' => 'desc'));
            } else {
                $this->paginate = array('limit' => PAGE_LIMIT, 'order' => array('created' => 'desc'));
            }
            $this->Wallpaper->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id',
                        'fields' => array('full_name', 'id')
                    )
                )
                    ), false
            );
            $this->Wallpaper->recursive = 1;
            $this->set('WallpaperesArr', $this->paginate('Wallpaper'));
        } catch (NotFoundException $e) {
            $this->Session->setFlash('Please check, Something is wrong.', 'flashError');
            $this->redirect("/Wallpaperes");
        }
        $this->set('title_for_layout', 'Wallpaper');
    }

}
