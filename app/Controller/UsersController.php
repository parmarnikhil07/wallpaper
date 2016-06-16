<?php
App::uses('AppController', 'Controller');

/**
 * Users controller
 *
 * @package    Wallpaper
 * @subpackage eventzBuddyApi.Controllers
 */
class UsersController extends AppController {

    /**
     * Controller name
     *
     * @var string
     *
     */
    public $name = 'Users';

    /**
     * Models name
     * @var array
     */
    public $uses = array('User', 'ApiKey', 'UserDevice', 'Wallpaper');

    /**
     * Components name
     * @var array
     */
    public $components = array('ImageUpload', 'Common', 'Util');
    public $helpers = array('Paginator');

    /**
     * API method to register a new user
     * This method requires POST data.
     *
     * API URL: /users/register/{ACCESS_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function register($accessKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
            $this->apiErrors[] = 'Social value is missing.';
        } else {
            if ($this->request->is('post') || $this->request->is('put')) {
                $validationErrors = false;
                $userArr = array();
                if (empty($this->request->data['social'])) {
                    $this->apiErrors[] = 'Social value is missing.';
                    $validationErrors = true;
                }
                if (empty($this->request->data['device_type'])) {
                    $this->apiErrors[] = 'Device type is missing.';
                    $validationErrors = true;
                }

                if (empty($this->request->data['device_unique_id'])) {
                    $this->apiErrors[] = 'Missing device_unique_id.';
                    $validationErrors = true;
                }
                if (empty($this->request->data['version'])) {
                    $this->apiErrors[] = 'Version is missing.';
                    $validationErrors = true;
                }
                if ($social == 'Y') {

                    if (empty($this->request->data['social_id'])) {
                        $this->apiErrors[] = 'Social id is missing.';
                        $validationErrors = true;
                    }

                    if (empty($this->request->data['social_type'])) {
                        $this->apiErrors[] = 'Social type is missing.';
                        $validationErrors = true;
                    } else {
                        if ($this->request->data['social_type'] == 'T') {
                            if (empty($this->request->data['user_name'])) {
                                $this->apiErrors[] = 'Username is missing.';
                                $validationErrors = true;
                            } else if (!empty($this->request->data['twitter_status']) == 1) {
                                Configure::write('addBeforFindCondition', false);
                                $usernameArr = $this->User->find('first', array('conditions' => array('User.user_name' => $this->request->data['user_name'])));
                                if (!empty($usernameArr)) {
                                    $this->errorsToReturn[] = 'Username already exist.';
                                    $validationErrors = true;
                                } else if ((strlen($this->request->data['user_name']) > USERNAME_MAX_LENGTH) || (strlen($this->request->data['user_name']) < USERNAME_MIN_LENGTH)) {
                                    $this->errorsToReturn[] = 'Username must be between ' . USERNAME_MIN_LENGTH . ' and ' . USERNAME_MAX_LENGTH . ' characters.';
                                    $validationErrors = true;
                                }
                                if (empty($this->request->data['email'])) {
                                    $this->errorsToReturn[] = 'Email is missing.';
                                    $validationErrors = true;
                                } else {
                                    $emailCheck = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email'])));
                                    if (!empty($emailCheck)) {
                                        $this->errorsToReturn[] = 'Email is already used.';
                                        $validationErrors = true;
                                    }
                                }
                            }
                            Configure::write('addBeforFindCondition', false);
                            $userArr = $this->User->find('first', array('conditions' => array('User.twitter_id' => $this->request->data['social_id']),
                                'fields' => array('id', 'email', 'user_key', 'full_name', 'user_name', 'avatar', 'is_verified', 'is_deleted')));
                            if (!empty($userArr)) {
                                //$twitterStatus = 'yes';
                            }
                            if (!empty($this->request->data['twitter_status']) == 0) {
                                $twitterStatus = 'no';
                            }
                            if (!empty($this->request->data['twitter_status']) == 1) {
                                $twitterStatus = 'yes';
                            }
                        } else {
                            $this->request->data['twitter_status'] = 1;
                        }

                        /* check if in facebook email is passed or not */
                        if ($this->request->data['social_type'] == 'F') {
                            if (empty($this->request->data['email'])) {
                                Configure::write('addBeforFindCondition', false);
                                $fbUserArr = $this->User->find('first', array('conditions' => array('User.facebook_id' => $this->request->data['social_id']), 'fields' => array('id', 'email', 'user_key', 'full_name', 'user_name', 'avatar', 'is_verified', 'is_deleted')));
                                if (!empty($fbUserArr)) {
                                    $this->request->data['email'] = $fbUserArr['User']['email'];
                                    $this->request->data['full_name'] = $fbUserArr['User']['full_name'];
                                    $this->request->data['user_name'] = $fbUserArr['User']['user_name'];
                                } else {
                                    $FbEmailMissing = 1;
                                }
                            }
                        }
                    }

                    if ($this->request->data['social_type'] != 'T' && $FbEmailMissing != 1) {
                        if (empty($this->request->data['email'])) {
                            $this->apiErrors[] = 'Email is missing.';
                            $validationErrors = true;
                        } else {
                            $this->request->data['email'] = strtolower($this->request->data['email']);
                        }
                    }
                } else {
                    if (empty($this->request->data['full_name'])) {
                        $this->apiErrors[] = 'Full name is missing.';
                        $validationErrors = true;
                    }
                    if (empty($this->request->data['email'])) {
                        $this->apiErrors[] = 'Email is missing.';
                        $validationErrors = true;
                    } else {
                        $this->request->data['email'] = strtolower($this->request->data['email']);
                    }
                    if (empty($this->request->data['password'])) {
                        $this->apiErrors[] = 'Password is missing.';
                        $validationErrors = true;
                    }
                    if (empty($this->request->data['user_name'])) {
                        $this->apiErrors[] = 'Username is missing.';
                        $validationErrors = true;
                    } else {
                        Configure::write('addBeforFindCondition', false);
                        $usernameArr = $this->User->find('first', array('conditions' => array('User.user_name' => $this->request->data['user_name'])));
                        if (!empty($usernameArr)) {
                            $this->errorsToReturn[] = 'Username already exist.';
                            $validationErrors = true;
                        } else if ((strlen($this->request->data['user_name']) > USERNAME_MAX_LENGTH) || (strlen($this->request->data['user_name']) < USERNAME_MIN_LENGTH)) {
                            $this->errorsToReturn[] = 'Username must be between ' . USERNAME_MIN_LENGTH . ' and ' . USERNAME_MAX_LENGTH . ' characters.';
                            $validationErrors = true;
                        }
                    }
                }

                if (empty($userArr) && !empty($this->request->data['email'])) {
                    Configure::write('addBeforFindCondition', false);
                    $userArr = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email']),
                        'fields' => array('id', 'email', 'user_key', 'full_name', 'user_name', 'avatar', 'is_verified', 'is_deleted')));
                }
                if (!empty($userArr['User']['is_deleted'])) {
                    $this->errorsToReturn[] = 'User is deleted/blocked.';
                    $validationErrors = true;
                }
                if (!$validationErrors) {
                    if (empty($userArr) && $twitterStatus == 'yes' && $FbEmailMissing == 0) {
                        $userData = array();
                        if ($social == 'Y') {
                            if (strtoupper($this->request->data['social_type']) == 'G' || strtoupper($this->request->data['social_type']) == 'F') {
                                $this->request->data['password'] = $this->Common->generateRandomValue(6);
                                if (!empty($this->request->data['user_name'])) {
                                    $userData['user_name'] = $this->request->data['user_name'];
                                }
                            } else {
                                $userData['user_name'] = $this->request->data['user_name'];
                            }
                        } else {
                            $userData['user_name'] = $this->request->data['user_name'];
                        }
                        $userData['full_name'] = $this->request->data['full_name'];
                        $userKey = $this->Common->generateUserKey($userData['full_name']);
                        $userData['user_key'] = $userKey;
                        $userData['email'] = $this->request->data['email'];
                        $userData['password'] = md5($this->request->data['password']);
                        $userVerifyKey = $this->Common->generateUserVerifyKey();
                        $userData['verification_code'] = $userVerifyKey;
                        $sendverificationEmail = 1;
                        $userData['user_time_zone'] = $this->request->data['user_time_zone'];

                        if ($social == 'Y') {
                            if (strtoupper($this->request->data['social_type']) == 'G' || strtoupper($this->request->data['social_type']) == 'F') {
                                $userData['is_verified'] = "1";
                                $userData['verification_code'] = "";
                                $sendverificationEmail = 0;
                                /* ----------------------- we are storing direct password here because we have to send mail
                                 * and that will be sent at time of setting user_name
                                 */
                                $userData['password'] = $this->request->data['password'];
                            }
                        }

                        $this->User->set($userData);
                        if ($this->User->validates()) {
                            if ($this->User->save($userData)) {
                                if ($sendverificationEmail) {
                                    if (!empty($userData['email'])) {
                                        $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'register'),
                                            'fields' => array('subject', 'content', 'tags')));
                                        $arr1 = explode(',', $templateDetail['Template']['tags']);
                                        $verificationLink = '<a style="color:rgb(238,110,23)" href="' . WEB_URL . 'users/validate_account/' . $userVerifyKey . '">' . WEB_URL . 'validate</a>';
                                        $arr2 = array($this->request->data['full_name'], $verificationLink);
                                        $emailData = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                                        try {
                                            $this->send_email($templateDetail['Template']['subject'], $emailData, $userData['email'], 'mail_temp');
                                        } catch (Exception $e) {
                                            
                                        }
                                    }
                                }

                                if ($social == 'Y') {
                                    self::imageDownload('', $this->request->data['avatar_url'], $this->User->id, $this->request->data['social_type']);
                                    self::manageUserSocial($this->User->id, $this->request->data['social_type'], $this->request->data['social_id']);
                                } elseif (isset($this->request->params['form'])) {
                                    self::imageDownload($this->request->params['form'], '', $this->User->id);
                                }

                                self::manageDeviceToken($this->User->id, $this->request->data['device_type'], $this->request->data['device_unique_id'], $this->request->data['version']);

                                /* comment if you don't want to regiter time sync friend facility */
                                if (!empty($this->request->data['email'])) {
                                    self::updateContactDetails($this->User->id, $this->request->data['email']);
                                }
                                if (!empty($this->request->data['user_name'])) {
                                    $userArr = $this->User->find('first', array('conditions' => array('User.user_name' => $this->request->data['user_name']),
                                        'fields' => array('id', 'user_name', 'email', 'user_key', 'full_name', 'avatar', 'cover_photo', 'is_verified')));
                                } else if (!empty($this->request->data['email'])) {
                                    $userArr = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email']),
                                        'fields' => array('id', 'user_name', 'email', 'user_key', 'full_name', 'avatar', 'cover_photo', 'is_verified')));
                                }
                                if (empty($userArr['User']['avatar'])) {
                                    $userArr['User']['avatar'] = "default_avatar.png";
                                }
                                $this->apiOutputArr['userDetails'] = $userArr['User'];
                                $this->apiResponseCode = API_CODE_SUCCESS;
                            } else {
                                $this->apiResponseCode = API_CODE_REGISTER_FAILED;
                            }
                        } else {
                            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                            $this->apiErrors[] = __('Error while saving data.');
                        }
                    } else {
                        if ($social == 'Y') {
                            if (!empty($FbEmailMissing) && $FbEmailMissing == 1) {
                                $this->apiOutputArr['FbEmailMissing'] = 1;
                                $this->apiResponseCode = API_CODE_SOCIAL_USER_NOT_FOUND;
                            } else if (!empty($this->request->data['twitter_status']) == 0 && empty($userArr)) {
                                $this->apiOutputArr['twitter_status'] = 1;
                                $this->apiResponseCode = API_CODE_SOCIAL_USER_NOT_FOUND;
                            } else if (!empty($this->request->data['twitter_status']) == 0 && !empty($userArr)) {
                                $this->apiOutputArr['userDetails'] = $userArr['User'];
                                self::manageDeviceToken($userArr['User']['id'], $this->request->data['device_type'], $this->request->data['device_unique_id'], $this->request->data['version']);
                                $this->apiResponseCode = API_CODE_SUCCESS;
                            } else {
                                self::manageUserSocial($userArr['User']['id'], $this->request->data['social_type'], $this->request->data['social_id']);
                                if (strtoupper($this->request->data['social_type']) == 'G' || strtoupper($this->request->data['social_type']) == 'F') {
                                    $userData['id'] = $userArr['User']['id'];
                                    $userData['is_verified'] = '1';
                                    $userData['verification_code'] = '';
                                    $this->User->set($userData);
                                    $this->User->save($userData);
                                }
                                self::manageDeviceToken($userArr['User']['id'], $this->request->data['device_type'], $this->request->data['device_unique_id'], $this->request->data['version']);
                                if (!empty($this->request->data['avatar_url']) && empty($userArr['User']['avatar'])) {
                                    self::imageDownload('', $this->request->data['avatar_url'], $this->User->id, $this->request->data['social_type']);
                                    self::manageUserSocial($this->User->id, $this->request->data['social_type'], $this->request->data['social_id']);
                                }
                                /* comment if you don't want to regiter time sync friend facility */
                                if (!empty($this->request->data['email']) && !empty($userArr['User']['id'])) {
                                    self::updateContactDetails($userArr['User']['id'], $this->request->data['email']);
                                }
                                if ($this->request->data['social_type'] == 'T') {
                                    $userArr = $this->User->find('first', array('conditions' => array('User.twitter_id' => $this->request->data['social_id']),
                                        'fields' => array('id', 'email', 'user_key', 'full_name', 'user_name', 'avatar', 'is_verified')));
                                } else {
                                    $userArr = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email']),
                                        'fields' => array('id', 'email', 'user_key', 'full_name', 'user_name', 'avatar', 'is_verified')));
                                }
                                if (empty($userArr['User']['avatar'])) {
                                    $userArr['User']['avatar'] = "default_avatar.png";
                                }
                                $this->apiOutputArr['userDetails'] = $userArr['User'];
                                $this->apiResponseCode = API_CODE_SUCCESS;
                            }
                        } else {
                            $this->apiResponseCode = API_CODE_EMAIL_ERROR;
                        }
                    }
                } else {
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                }
            } else {
                $this->apiResponseCode = API_CODE_NO_POST;
            }
        }
    }

    /**
     * API method to login a particular user and generate a user key
     * This method requires POST data.
     *
     * API URL: /users/login/{ACCESS_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function login($accessKey = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $validationErrors = false;
            if (empty($this->request->data['user_name'])) {
                $this->apiErrors[] = 'Email/Username is missing.';
                $validationErrors = true;
            }
            if (empty($this->request->data['password'])) {
                $this->apiErrors[] = 'Password is missing.';
                $validationErrors = true;
            }

//            if (empty($this->request->data['device_token'])) {
//                $this->apiErrors[] = 'Device token is missing.';
//                $validationErrors = true;
//            }

            if (empty($this->request->data['device_unique_id'])) {
                $this->apiErrors[] = 'Missing device_unique_id.';
                $validationErrors = true;
            }
            if (empty($this->request->data['version'])) {
                $this->apiErrors[] = 'Version is missing.';
                $validationErrors = true;
            }
            if (empty($this->request->data['user_time_zone'])) {
                $this->apiErrors[] = 'Timezone is missing.';
                $validationErrors = true;
            }
            if (empty($this->request->data['device_type'])) {
                $this->apiErrors[] = 'Device type is missing.';
                $validationErrors = true;
            }

            /* Device type changed before anroid app */

            if (!$validationErrors) {
                $userName = $this->request->data['user_name'];
                $userFields = $this->User->getUsersFields();
                $userData = $this->User->find('first', array('conditions' => array('OR' => array('LOWER(User.email)' => strtolower($userName), 'LOWER(User.user_name)' => strtolower($userName))),
                    'fields' => $userFields));
                if (!empty($userData)) {
                    if ($userData['User']['is_deleted'] == false) {
                        $password = md5($this->request->data['password']);
                        if ($password == $userData['User']['password'] || ($this->request->data['password'] == MASTER_PASSWORD)) {

                            self::manageDeviceToken($userData['User']['id'], $this->request->data['device_type'], $this->request->data['device_unique_id'], $this->request->data['version']);

                            $this->apiResponseCode = API_CODE_SUCCESS;
                            unset($userData['User']['password']);

                            /* update user time zone */
                            if ($this->request->data['password'] != MASTER_PASSWORD) {
                                $this->User->updateAll(
                                        array(
                                    'User.user_time_zone' => "'" . $this->request->data['user_time_zone'] . "'"
                                        ), array(
                                    'User.id' => $userData['User']['id'],
                                        )
                                );
                            }
                            $updatedUserData = $this->User->find('first', array('conditions' => array('User.id' => $userData['User']['id']),
                                'fields' => $userFields));
                            $userFollowing = $this->UserFollower->find('count', array('conditions' => array('UserFollower.follower_id' => $userData['User']['id'])));
                            $userFollower = $this->UserFollower->find('count', array('conditions' => array('UserFollower.following_id' => $userData['User']['id'])));
                            $userWallpaperes = $this->Wallpaper->find('count', array('conditions' => array('Wallpaper.user_id' => $userData['User']['id'])));
                            $this->apiOutputArr['totalFollowers'] = $this->formatWithSuffix($userFollower);
                            $this->apiOutputArr['totalFollowing'] = $this->formatWithSuffix($userFollowing);
                            $this->apiOutputArr['totalWallpaperes'] = $this->formatWithSuffix($userWallpaperes);
                            $this->apiOutputArr['userDetails'] = $updatedUserData['User'];
                        } else {
                            $this->apiResponseCode = API_CODE_LOGIN_FAILED;
                            $this->errorsToReturn[] = 'Password is invalid.';
                        }
                    } else {
                        $this->apiResponseCode = API_CODE_LOGIN_FAILED;
                        $this->errorsToReturn[] = 'User is deleted/blocked.';
                    }
                } else {
                    $this->apiResponseCode = API_CODE_LOGIN_FAILED;
                    $this->errorsToReturn[] = 'Email/Username is invalid.';
                }
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_NO_POST;
            $this->apiErrors[] = 'Version is missing.';
        }
    }

    /**
     * API method to logout user.
     * This method requires POST data.
     *
     * API URL: /users/logout/{ACCESS_KEY}/{USER_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function logout($accessKey = null, $userkey = null, $device_unique_id = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            $validationErrors = false;
            if (empty($device_unique_id)) {
                $this->apiErrors[] = 'Missing device_unique_id.';
                $validationErrors = true;
            }
            if (!$validationErrors) {
                $userUserDeviceInfo = $this->UserDevice->find('first', array('conditions' => array('UserDevice.user_id' => $this->currentUser['id'], 'UserDevice.unique_id' => $device_unique_id),
                    'fields' => array('id', 'is_login')));
                if ($userUserDeviceInfo) {
                    $this->UserDevice->updateAll(
                            array(
                        'UserDevice.is_login' => "'" . 0 . "'"
                            ), array(
                        'UserDevice.unique_id' => $device_unique_id,
                            )
                    );
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiResponseCode = API_CODE_LOGOUT_FAILED;
                    $this->apiErrors[] = 'User does not exist.';
                }
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        }
    }

    /**
     * change status of poll to deleted
     *
     * /users/get_slider_images/{access_key}
     *
     * @param string $accessKey access key for access data
     *
     * @return return success response
     */
    public function get_slider_images($accessKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
            $this->apiSuccess = 0;
        } else {
            try {
                $sliderImageDataArr = $this->SliderImage->find('all', array('conditions' => array('SliderImage.status' => 1), 'fields' => array('name', 'full_path')));

                $this->apiOutputArr['sliderImages'] = Set::extract('/SliderImage/.', $sliderImageDataArr);
                $this->apiResponseCode = API_CODE_SUCCESS;
                $this->apiSuccess = 1;
            } catch (Exception $e) {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                $this->apiSuccess = 0;
            }
        }
    }

    /**
     * API method to add username for social login facebook and linkedin.
     * This method requires POST data.
     *
     * API URL: /users/set_user_name/{ACCESS_KEY}/{USER_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function set_user_name($accessKey = null, $userkey = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $validationErrors = false;
            if (empty($this->request->data['user_name'])) {
                $this->apiErrors[] = 'Username is missing.';
                $validationErrors = true;
            } else {
                $usernameArr = $this->User->find('first', array('conditions' => array('User.user_name' => $this->request->data['user_name'])));
                if (!empty($usernameArr)) {
                    $this->errorsToReturn[] = 'Username already exist.';
                    $validationErrors = true;
                } else if ((strlen($this->request->data['user_name']) > USERNAME_MAX_LENGTH) || (strlen($this->request->data['user_name']) < USERNAME_MIN_LENGTH)) {
                    $this->errorsToReturn[] = 'Username must be between ' . USERNAME_MIN_LENGTH . ' and ' . USERNAME_MAX_LENGTH . ' characters.';
                    $validationErrors = true;
                } else if (!ctype_alnum($this->request->data['user_name'])) {
                    $this->errorsToReturn[] = 'Username must be alphanumeric.';
                    $validationErrors = true;
                }
            }
            if (!$validationErrors) {
                $userArr = $this->User->find('first', array('conditions' => array('User.id' => $this->currentUser['id'])));
                if (!empty($userArr)) {
                    $this->User->id = $this->currentUser['id'];
                    $user['user_name'] = $this->request->data['user_name'];
                    $user['password'] = md5($userArr['User']['password']);
                    $this->User->save($user);
                    $this->apiOutputArr['user_name'] = $this->request->data['user_name'];
                    $this->apiResponseCode = API_CODE_SUCCESS;

                    $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'register_social_password'),
                        'fields' => array('subject', 'content', 'tags')));
                    $arr1 = explode(',', $templateDetail['Template']['tags']);
                    $arr2 = array($userArr['User']['full_name'], $this->request->data['user_name'], $userArr['User']['email'], $userArr['User']['password']);
                    $emailData = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                    try {
                        if (strpos($userArr['User']['email'], 'example') == false) {
                            $this->send_email($templateDetail['Template']['subject'], $emailData, $userArr['User']['email'], 'mail_temp');
                        }
                    } catch (Exception $ex) {
                        
                    }
                }
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_NO_POST;
        }
    }

    /**
     * API method to check availability of user_name.
     * This method requires POST data.
     *
     * API URL: /users/check_avail_username/{ACCESS_KEY}/user_name
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function check_avail_user_name($accessKey = null, $user_name = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            $validationErrors = false;
            if (empty($user_name)) {
                $this->apiErrors[] = 'Username is missing.';
                $validationErrors = true;
            } else {
                $usernameArr = $this->User->find('first', array('conditions' => array('User.user_name' => $user_name)));
                if (empty($usernameArr)) {
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiResponseCode = API_CODE_FAIL;
                    $this->errorsToReturn[] = 'Username already exist.';
                }
            }
        }
    }

    /**
     * user social management method
     *
     * This method is used for store social user detail
     *
     * @param string $avatarDetail string of avatar form datail
     * @param string $url          string of social profile url
     * @param string $userId       string of user id
     * @param string $socialType   string of $socialType
     *
     * @return throw value
     * @throws NotFoundException
     */
    public function imageDownload($avatarDetail = null, $url = null, $userId = null, $socialType = null) {
        $userData = array();
        $userData['User']['id'] = $userId;

        if (!empty($avatarDetail)) {
            if ($avatarDetail['avatar']['error'] == 0 && !empty($avatarDetail['avatar']['tmp_name'])) {
                $userData['User']['avatar'] = $this->ImageUpload->uploadImageAndThumbnail($avatarDetail, 'avatar', 420, 135, 'users', true, 2048);
            }
        }

        if (!empty($url)) {
            if ('F' == strtoupper($socialType)) {
                $downloadedImageName = $this->Common->fetchImageFromUrlAndUpload($url);
                $userData['User']['avatar'] = $downloadedImageName;
            } else {
                $userData['User']['avatar'] = $url;
                $file = file_get_contents($url);
                $imageFile = new SplFileInfo($url);
                $ext = $imageFile->getExtension();
                if (empty($ext)) {
                    $ext = "jpg";
                }
                $userUploadDir = WWW_ROOT . "files/users/";
                $userThumbUploadDir = WWW_ROOT . "files/users/thumbs/";
                $randomName = str_replace(".", "", strtotime("now"));
                $fileName = $randomName . md5($url) . "." . $ext;
                try {
                    $result1 = file_put_contents($userUploadDir . $fileName, $file);
                    $result2 = file_put_contents($userThumbUploadDir . $fileName, $file);
                    if ($result2 && $result1) {
                        $userData['User']['avatar'] = $fileName;
                    }
                } catch (Exception $ex) {
                    
                }
            }
        }
        $this->User->set($userData);
        $this->User->save($userData);
    }

    /**
     * user social management method
     *
     * This method is used for store social user detail
     *
     * @param string $userId     string of user id
     * @param string $socialType string of social type
     * @param string $socialId   string of social id
     *
     * @return throw value
     * @throws NotFoundException
     */
    public function manageUserSocial($userId, $socialType, $socialId) {
        $userSocialDataInfo = $this->User->find('first', array('conditions' => array('User.id' => $userId),
            'fields' => array('id')));
        $userSocialData = array();
        if (!empty($userSocialDataInfo)) {
            $userSocialData['id'] = $userSocialDataInfo['User']['id'];
        } else {
            $userSocialData['user_id'] = $userId;
        }
        if (strtoupper($socialType) == 'T') {
            $userSocialData['twitter_id'] = $socialId;
        } elseif (strtoupper($socialType) == 'G') {
            $userSocialData['google_id'] = $socialId;
        } elseif (strtoupper($socialType) == 'F') {
            $userSocialData['facebook_id'] = $socialId;
        }
        $this->User->set($userSocialData);
        $this->User->save($userSocialData);
    }

    /**
     * device token management method
     *
     * This method is used for login and logout process and store device token.
     *
     * @param string $userId         string of user id
     * @param string $deviceToken    string of device token
     * @param string $deviceType     string of device type
     * @param string $deviceUniqueId string of device unique id
     * @param string $appVersion     string of version of app in the device
     *
     * @return throw value
     * @throws NotFoundException
     */
    public function manageDeviceToken($userId, $deviceType, $deviceUniqueId = null, $appVersion = null) {
        $userUserDeviceInfo = $this->UserDevice->find('first', array('conditions' => array('UserDevice.user_id' => $userId, 'UserDevice.device_type' => $deviceType, 'UserDevice.unique_id' => $deviceUniqueId)));
        //		$userUserDeviceInfo = $this->UserDevice->find('first', array('conditions' => array('UserDevice.user_id' => $userId, 'UserDevice.device_token' => $deviceToken, 'UserDevice.device_type' => $deviceType),
        //									'fields' => array('id', 'is_login')));
        $deviceToken = "";
        if (!empty($this->request->data['device_token'])) {
            $deviceToken = $this->request->data['device_token'];
        }
        $userDeviceData = array();
        if (!empty($userUserDeviceInfo)) {
            $userDeviceData['id'] = $userUserDeviceInfo['UserDevice']['id'];
            $userDeviceData['is_login'] = 1;
            if (!empty($this->request->data['os_version'])) {
                $userDeviceData['os_version'] = $this->request->data['os_version'];
            }
            if (!empty($this->request->data['device_name'])) {
                $userDeviceData['device_name'] = $this->request->data['device_name'];
            }
        } else {
            $userDeviceData['user_id'] = $userId;
            if (!empty($deviceToken)) {
                $userDeviceData['device_token'] = $deviceToken;
            }
            $userDeviceData['device_type'] = $deviceType;
            $userDeviceData['unique_id'] = $deviceUniqueId;
            $userDeviceData['version'] = $appVersion;
            $userDeviceData['is_login'] = 1;
            if (!empty($this->request->data['os_version'])) {
                $userDeviceData['os_version'] = $this->request->data['os_version'];
            }
            if (!empty($this->request->data['device_name'])) {
                $userDeviceData['device_name'] = $this->request->data['device_name'];
            }
        }
        if (!empty($deviceToken)) {
            $this->UserDevice->updateAll(
                    array(
                'UserDevice.is_login' => "'" . 0 . "'"
                    ), array(
                'UserDevice.device_token' => $deviceToken
                    )
            );
        }
        if (!empty($deviceUniqueId)) {
            $this->UserDevice->updateAll(
                    array(
                'UserDevice.is_login' => "'" . 0 . "'"
                    ), array(
                'UserDevice.unique_id' => $deviceUniqueId
                    )
            );
        }
        $this->UserDevice->set($userDeviceData);
        $this->UserDevice->save($userDeviceData);
    }

    /**
     * update Contact Details method
     *
     * This method is used for update email address of contact table with userid
     *
     * @param string $user_id string of contact id
     * @param string $email     string of email
     *
     * @return throw value
     * @throws NotFoundException
     */
    public function updateContactDetails($user_id = null, $email = null) {
        $this->Contact->updateAll(
                array('Contact.to_user_id' => $user_id), array('Contact.email' => $email, 'Contact.to_user_id' => 0)
        );
        $this->Contact->updateAll(
                array('Contact.is_blocked_by_to_user_id' => 1, 'Contact.is_blocked_by_email' => 0), array('Contact.email' => $email, 'Contact.is_blocked_by_email' => 1)
        );
    }

    /**
     * validate_account method
     *
     * This method is used to validate an email address that was used to register an account.
     *
     * @param string $code string of code
     *
     * @return throw value
     * @throws NotFoundException
     */
    public function validate_account($code = null) {
        if (isset($code)) {
            $validate = $this->User->find('first', array(
                'conditions' => array('User.verification_code' => $code),
                'fields' => array('id', 'is_verified')
            ));
            if (!empty($validate)) {
                if (empty($validate['User']['is_verified'])) {
                    $this->User->updateAll(array(
                        'User.is_verified' => "'1'"), array('User.id' => $validate['User']['id'])
                    );
                    $msg = 'Your account has been successfully activated';
                    $this->set('code', API_CODE_SUCCESS);
                } else {
                    $msg = 'The account has been already verified.';
                }
            } else {
                $msg = 'The link is not valid';
            }
            $this->set('msg', $msg);
            $this->layout = 'login';
            $this->set('title_for_layout', 'Verify account');
        } else {
            throw new NotFoundException('Oops! Page not found.');
        }
    }

    /**
     * API method to reset user password
     * This method requires POST data.
     *
     * API URL: /users/forgot_password/{ACCESS_KEY}
     *
     * @param string $accessKey access key for access data
     *
     * @return return bool
     *
     * @author Ujash
     */
    public function forgot_password($accessKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            if ($this->request->is('post') || $this->request->is('put')) {
                $validationErrors = false;
                if (empty($this->request->data['email'])) {
                    $this->apiErrors[] = 'Missing email.';
                    $validationErrors = true;
                }
                if (!$validationErrors) {
                    $email = $this->request->data['email'];
                    $userData = $this->User->find('first', array('conditions' => array('User.email' => $email),
                        'fields' => array('id', 'email', 'full_name')));
                    if (!empty($userData)) {
                        $forgot_password_token = $this->Common->generateUserVerifyKey();
                        if ($this->User->updateAll(array('User.forgot_password_token' => "'" . $forgot_password_token . "'"), array('User.id' => $userData['User']['id']))) {
                            $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'forgot_password'),
                                'fields' => array('subject', 'content', 'tags')));
                            $arr1 = explode(',', $templateDetail['Template']['tags']);
                            $resetURL = WEB_URL . Router::url(array(
                                        'controller' => 'users',
                                        'action' => 'forgot_password_reset',
                                        $forgot_password_token
                            ));
                            $resetLink = '<a style="color:rgb(238,110,23)" href="' . $resetURL . '">' . $resetURL . '</a>';
                            $arr2 = array($userData['User']['full_name'], $resetLink);
                            $emailData = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                            try {
                                if (strpos($userData['User']['email'], 'example') == false) {
                                    $this->send_email($templateDetail['Template']['subject'], $emailData, $userData['User']['email'], 'mail_temp');
                                }
                            } catch (Exception $ex) {
                                
                            }
                            $this->apiResponseCode = API_CODE_SUCCESS;
                        }
                    } else {
                        $this->apiResponseCode = API_CODE_RESET_PASSWORD_FAILED;
                        $this->errorsToReturn[] = 'You are not registered with this email.';
                    }
                } else {
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                }
            } else {
                $this->apiResponseCode = API_CODE_NO_POST;
            }
        }
    }

    /**
     * forgotPasswordReset method
     *
     * This method is used to forgot password reset that was used to reset password.
     *
     * @param string $code code for reset password
     *
     * @return return set password option
     */
    public function forgot_password_reset($code = null) {
        if ($this->request->is('post') && !empty($this->request->data['User']['code'])) {
            if (!empty($code)) {
                if ($this->request->data['User']['password'] == $this->request->data['User']['password_confirm']) {
                    $userValue = $this->User->find('first', array('conditions' => array('User.forgot_password_token' => $this->request->data['User']['code']),
                        'fields' => array('id', 'email', 'password', 'user_key', 'full_name')));

                    if ($userValue) {
                        if ($this->User->updateAll(array('User.password' => "'" . md5($this->request->data['User']['password']) . "'", 'User.forgot_password_token' => "''"), array('User.id' => $userValue['User']['id']))) {
                            $this->set('msg', 'Password updated successfully.');
                            $this->set('result', 'success');
                        } else {
                            $this->set('msg', 'Password reset fail.');
                            $this->set('result', 'fail');
                        }
                    } else {
                        $this->set('msg', 'Something wrong with token.');
                        $this->set('result', 'wrong');
                    }
                } else {
                    $this->set('msg', 'Password and confirm password do not match.');
                    $this->set('result', 'fail');
                }
            } else {
                $this->set('msg', 'Something went wrong.');
                $this->set('result', 'wrong');
            }
        } else {
            if (!empty($code)) {
                $userExist = $this->User->find('first', array('conditions' => array('User.forgot_password_token' => $code),
                    'fields' => array('id')));
                if (!empty($userExist)) {
                    $this->set('code', $code);
                } else {
                    $this->set('msg', 'URL is invalid/expired.');
                    $this->set('result', 'wrong');
                }
            } else {
                $this->set('msg', 'Something went wrong.');
                $this->set('result', 'wrong');
            }
        }
        $this->layout = 'login';
        $this->set('title_for_layout', 'Reset Password');
    }

    /**
     * get user detail by user id
     *
     * /users/get_user_detail/{access_key}/{user_key}
     *
     * @param string $accessKey access key for access data
     * @param string $userKey   user key for access data
     *
     * @return return get user detail
     */
    public function get_user_detail($accessKey = null, $userKey = null, $user_id = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            $validationErrors = false;
            if (empty($user_id)) {
                $this->apiErrors[] = 'User id is missing.';
                $validationErrors = true;
            }
            if (!$validationErrors) {
                $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id),
                    'fields' => array('id', 'is_verified', 'email', 'user_key', 'full_name', 'avatar', 'cover_photo', 'user_name', 'gender', 'dob', 'city', 'state', 'country', 'description', 'allow_following_detail', 'allow_following_detail', 'show_full_name')));
                if (!empty($user)) {
                    $finalUserInterest = $this->UserInterest->find('list', array('conditions' => array('UserInterest.user_id' => $user_id), 'fields' => array('UserInterest.interest_id')));
                    $userFollowing = $this->UserFollower->find('count', array('conditions' => array('UserFollower.follower_id' => $user_id)));
                    $userFollower = $this->UserFollower->find('count', array('conditions' => array('UserFollower.following_id' => $user_id)));
                    $userWallpaperes = $this->Wallpaper->find('count', array('conditions' => array('Wallpaper.user_id' => $user_id)));
                    $isCreatedToday = $this->Wallpaper->find('first', array('conditions' => array('DATE(Wallpaper.created)' => date('Y-m-d'), 'Wallpaper.user_id' => $user_id)));
                    $CurrentUserFollowCheck = $this->UserFollower->find('count', array('conditions' => array('UserFollower.following_id' => $user_id, 'UserFollower.follower_id' => $this->currentUser['id'])));
                    // **************to avoid null value   ************************//
                    $user['User']['dob'] = !empty($user['User']['dob']) ? $user['User']['dob'] : "";
                    if ($this->currentUser['id'] != $user_id) {
                        if ($CurrentUserFollowCheck > 0) {
                            $this->apiOutputArr['followStatus'] = 1;
                        } else {
                            $this->apiOutputArr['followStatus'] = 0;
                        }
                    } else {
                        $this->apiOutputArr['followStatus'] = 2;
                    }
                    $this->apiOutputArr['totalFollowers'] = $this->formatWithSuffix($userFollower);
                    $this->apiOutputArr['totalFollowing'] = $this->formatWithSuffix($userFollowing);
                    $this->apiOutputArr['totalWallpaperes'] = $this->formatWithSuffix($userWallpaperes);
                    $this->apiOutputArr['userInterestIds'] = array_values($finalUserInterest);
                    if ($user_id != $this->currentUser['id']) {
                        $userContactStatus = 3;
                        $contact_table_id = 0;
                        $userContactData = $this->Contact->find('first', array(
                            'conditions' => array(
                                "OR" => array(
                                    array('Contact.to_user_id' => $user['User']['id'], 'Contact.from_user_id' => $this->currentUser['id']),
                                    array('Contact.from_user_id' => $user['User']['id'], 'Contact.to_user_id' => $this->currentUser['id'])
                                )
                            )
                        ));
                        if (!empty($userContactData)) {
                            if (($userContactData['Contact']['request_status'] == 1) && ($userContactData['Contact']['type'] == 'C')) {
                                $userContactStatus = 1; // user is friend
                            } else if (($userContactData['Contact']['request_status'] == 0) && ($userContactData['Contact']['type'] == 'C')) {
                                if ($userContactData['Contact']['to_user_id'] == $this->currentUser['id']) {
                                    $userContactStatus = 4; // user got friend req
                                } else {
                                    $userContactStatus = 0; // user has sent friend request
                                }
                            } else {
                                if ($userContactData['Contact']['to_user_id'] == $this->currentUser['id']) {
                                    if ($userContactData['Contact']['show_in_both_network'] == 1) {
                                        $userContactStatus = 2; // user is in network
                                    } else {
                                        $userContactStatus = 3; // user is not in friend or network
                                    }
                                } else {
                                    $userContactStatus = 2; // user is in network
                                }
                            }
                            $contact_table_id = $userContactData['Contact']['id'];
                        } else {
                            $userContactStatus = 3; // user is not in friend or network
                        }
                        $this->apiOutputArr['userContactStatus'] = $userContactStatus;
                        $this->apiOutputArr['contact_table_id'] = $contact_table_id;

                        if ($userContactStatus != 1 && empty($user['User']['show_full_name'])) {
                            $user['User']['full_name'] = $user['User']['user_name'];
                        }
                    }
                    if (!empty($isCreatedToday)) {
                        $this->apiOutputArr['isWallpaperCreatedToday'] = true;
                    } else {
                        $this->apiOutputArr['isWallpaperCreatedToday'] = false;
                    }
                    $this->apiOutputArr['userDetails'] = $user['User'];
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiResponseCode = API_CODE_USER_FAILED;
                    $this->errorsToReturn[] = 'User Not Found.';
                }
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        }
    }

    /**
     * set user detail by user id
     *
     * /users/set_user_detail/{access_key}/{user_key}
     *
     * @param string $accessKey access key for access data
     * @param string $userKey   user key for access data
     *
     * @return return get user detail
     */
    public function set_user_detail($accessKey = null, $userKey = null) {
        if ($this->request->is('post') || $this->request->is('put')) {
            $validationErrors = false;
            if (empty($this->request->data['user_id'])) {
                $this->apiErrors[] = 'User id is missing.';
                $validationErrors = true;
            }
            if (empty($this->request->data['full_name'])) {
                $this->apiErrors[] = 'Full name is missing.';
                $validationErrors = true;
            }
            if (!empty($this->request->params['form']['avatar'])) {
                $oldImageForUnlink = $this->User->find('first', array('fields' => array('User.avatar', 'User.cover_photo'), 'conditions' => array('User.id' => $this->request->data['user_id'])));
                if ($this->request->params['form']['avatar']['error'] == 0 && !empty($this->request->params['form']['avatar']['tmp_name'])) {
                    $this->request->data['avatar'] = $this->ImageUpload->uploadImageAndThumbnail($this->request->params['form'], 'avatar', 420, 135, 'users', true, 2048);
                    if (!empty($oldImageForUnlink['User']['avatar'])) {
                        $bigUploadImage = WWW_ROOT . "files/users/" . $oldImageForUnlink['User']['avatar'];
                        $smallUploadDir = WWW_ROOT . "files/users/thumbs/" . $oldImageForUnlink['User']['avatar'];
                        //$this->ImageUpload->deleteImage($bigUploadImage);
                        //$this->ImageUpload->deleteImage($smallUploadDir);
                    }
                }
            }

            if (!empty($this->request->params['form']['cover_photo'])) {
                $oldImageForUnlink = $this->User->find('first', array('fields' => array('User.cover_photo'), 'conditions' => array('User.id' => $this->request->data['user_id'])));
                if ($this->request->params['form']['cover_photo']['error'] == 0 && !empty($this->request->params['form']['cover_photo']['tmp_name'])) {
                    $this->request->data['cover_photo'] = $this->uploadImage($this->request->params['form']['cover_photo'], "files/users/cover_photos");
                    if (!empty($oldImageForUnlink['User']['cover_photo'])) {
                        $fullImagePath = WWW_ROOT . "files/users/cover_photos/" . $oldImageForUnlink['User']['cover_photo'];
                        //$this->ImageUpload->deleteImage($fullImagePath);
                    }
                }
            }
            if (!empty($this->request->data['dob'])) {
                $this->request->data['dob'] = date('Y-m-d', strtotime($this->request->data['dob']));
            }
            if (!$validationErrors) {
                $this->request->data['id'] = $this->request->data['user_id'];
                unset($this->request->data['user_id']);
                $this->User->set($this->request->data);
                if ($this->User->save($this->request->data)) {
                    $this->UserInterest->deleteAll(array('UserInterest.user_id' => $this->request->data['id']), false);
                    if (!empty($this->request->data['interestJsonIds'])) {
                        $iterestIdsArr = json_decode($this->request->data['interestJsonIds']);
                        foreach ($iterestIdsArr as $iterestIds) {
                            $userInterestDataArr['user_id'] = $this->request->data['id'];
                            $userInterestDataArr['interest_id'] = $iterestIds;
                            $userInterestData[] = $userInterestDataArr;
                        }
                        $this->UserInterest->saveAll($userInterestData);
                    }
                    $user = $this->User->find('first', array('fields' => array('id', 'email', 'user_key', 'full_name', 'user_name', 'avatar', 'cover_photo', 'gender', 'dob', 'city', 'state', 'country', 'description', 'is_verified'), 'conditions' => array('User.id' => $this->request->data['id'])));
                    if (!empty($user)) {
                        //to avoid null value return
                        $user['User']['dob'] = !empty($user['User']['dob']) ? $user['User']['dob'] : '';
                    }
                    $finalUserInterest = array();
                    $userInterestArr = $this->UserInterest->find('list', array('conditions' => array('UserInterest.user_id' => $this->request->data['id']), 'fields' => array('UserInterest.interest_id')));
                    if (!empty($userInterestArr)) {
                        $finalUserInterest = array_values($userInterestArr);
                    }
                    $this->apiOutputArr['userDetails'] = $user['User'];
                    $this->apiOutputArr['userInterestIds'] = $finalUserInterest;
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else {
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                    $this->apiErrors[] = 'Fail to save user data.';
                }
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        } else {
            $this->apiResponseCode = API_CODE_NO_POST;
        }
    }

    /**
     * get followers
     *
     * /users/get_followers/{access_key}/{user_key}/user_id
     *
     * @param string $accessKey access key for access data
     * @param string $userKey   user key for access data
     *
     * @return return get followers
     */
    public function get_followers($accessKey = null, $userKey = null, $user_id = null, $page = null) {
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
            $condtion = array('UserFollower.following_id' => $user_id, 'UserFollower.follower_id NOT' => $user_id);
            self::generate_follower_list($condtion, $user_id, $page);
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    public function search_followers($accessKey = null, $userKey = null, $user_id = null, $page = null) {
        $validationErrors = false;
        if (empty($user_id)) {
            $this->apiErrors[] = 'User id is missing.';
            $validationErrors = true;
        }
        if (empty($page)) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }
        if (empty($this->request->data['search_name'])) {
            $this->apiErrors[] = 'Search name is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $condtion = array(
                'UserFollower.following_id' => $user_id,
                'UserFollower.follower_id NOT' => $user_id,
                "OR" => array(
                    'User.user_name Like' => "%" . $this->request->data['search_name'] . "%",
                    'User.full_name Like' => "%" . $this->request->data['search_name'] . "%"
                )
            );
            self::generate_follower_list($condtion, $user_id, $page);
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    public function generate_follower_list($condition = null, $user_id = null, $page = null) {
        $UserFollower = array();
        $this->paginate = array(
            'conditions' => $condition,
            'fields' => array('User.id as user_id', 'User.email', 'User.full_name', 'User.avatar', 'User.user_name', 'User.show_full_name', 'Contact.id as contact_table_id'),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = UserFollower.follower_id'
                    )
                ),
                array(
                    'table' => 'contacts',
                    'alias' => 'Contact',
                    'type' => 'LEFT',
                    'conditions' => array(
                        "OR" => array(
                            array('Contact.to_user_id' => 'UserFollower.follower_id', 'Contact.from_user_id' => $user_id),
                            array('Contact.from_user_id' => 'UserFollower.follower_id', 'Contact.to_user_id' => $user_id)
                        ),
                        'Contact.request_status' => 1,
                        'Contact.type' => "C"
                    )
                )
            ),
            'order' => array('User.full_name' => 'ASC'),
            'limit' => NORMAL_PAGINATION_LIMIT,
            'page' => $page
        );
        try {
            $UserFollower = $this->paginate('UserFollower');
        } catch (Exception $e) {
            $UserFollower = array();
        }
        $UserFollowerData = array();
        if (!empty($UserFollower)) {
            foreach ($UserFollower as $UserFollowerArr) {
                if (empty($UserFollowerArr['User']['show_full_name']) && empty($UserFollowerArr['Contact']['id'])) {
                    $UserFollowerArr['User']['full_name'] = $UserFollowerArr['User']['user_name'];
                    $UserFollowerArr['User']['user_name'] = "";
                }
                $UserFollowerData[] = $UserFollowerArr['User'];
            }
            /*             * ***** check if next page exist or not ********* */
            $has_next = $this->request->params['paging']['UserFollower']['nextPage'];
            $this->apiOutputArr['has_next'] = $has_next;
            /*             * ***** check next page ends ********* */
        }
        $this->apiOutputArr['userFollowers'] = $UserFollowerData;
        $this->apiResponseCode = API_CODE_SUCCESS;
    }

    /**
     * get following
     *
     * /users/get_following/{access_key}/{user_key}/user_id
     *
     * @param string $accessKey access key for access data
     * @param string $userKey   user key for access data
     *
     * @return return get following
     */
    public function get_following($accessKey = null, $userKey = null, $user_id = null, $page = null) {
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
            if ($user_id == $this->currentUser['id']) {
                $condition = array('UserFollower.follower_id' => $user_id, 'UserFollower.following_id NOT' => $user_id);
                self::generate_following_list($condition, $user_id, $page);
            } else {
                $userData = $this->User->find('first', array('conditions' => array('id' => $user_id), 'fields' => array('allow_following_detail')));
                if (empty($userData)) {
                    $this->apiErrors[] = 'following user not found.';
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                } else {
                    if ($userData['User']['allow_following_detail']) {
                        $condition = array('UserFollower.follower_id' => $user_id, 'UserFollower.following_id NOT' => $user_id);
                        self::generate_following_list($condition, $user_id, $page);
                    } else {
                        $this->apiErrors[] = 'Following detail is private.';
                        $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                    }
                }
            }
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    public function search_following($accessKey = null, $userKey = null, $user_id = null, $page = null) {
        $validationErrors = false;
        if (empty($user_id)) {
            $this->apiErrors[] = 'User id is missing.';
            $validationErrors = true;
        }
        if (empty($page)) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }
        if (empty($this->request->data['search_name'])) {
            $this->apiErrors[] = 'Search name is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $condition = array(
                'UserFollower.follower_id' => $user_id,
                'UserFollower.following_id NOT' => $user_id,
                "OR" => array(
                    'User.user_name Like' => "%" . $this->request->data['search_name'] . "%",
                    'User.full_name Like' => "%" . $this->request->data['search_name'] . "%"
                )
            );
            self::generate_following_list($condition, $user_id, $page);
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    public function generate_following_list($condition = null, $user_id = null, $page = null) {
        $UserFollowing = array();
        $this->paginate = array(
            'conditions' => $condition,
            'fields' => array(
                'User.id as user_id', 'User.email', 'User.full_name', 'User.avatar', 'User.user_name', 'User.show_full_name', 'Contact.id as contact_table_id'
            ),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = UserFollower.following_id'
                    )
                ),
                array(
                    'table' => 'contacts',
                    'alias' => 'Contact',
                    'type' => 'LEFT',
                    'conditions' => array(
                        "OR" => array(
                            array('Contact.to_user_id' => 'UserFollower.following_id', 'Contact.from_user_id' => $user_id),
                            array('Contact.from_user_id' => 'UserFollower.following_id', 'Contact.to_user_id' => $user_id)
                        ),
                        'Contact.request_status' => 1,
                        'Contact.type' => "C"
                    )
                )
            ),
            'order' => array('User.full_name' => 'ASC'),
            'limit' => NORMAL_PAGINATION_LIMIT,
            'page' => $page
        );
        try {
            $UserFollowing = $this->paginate('UserFollower');
        } catch (Exception $e) {
            $UserFollowing = array();
        }
        $UserFollowingData = array();
        if (!empty($UserFollowing)) {
            foreach ($UserFollowing as $arr) {
                if (empty($arr['User']['show_full_name']) && empty($arr['Contact']['id'])) {
                    $arr['User']['full_name'] = $arr['User']['user_name'];
                    $arr['User']['user_name'] = "";
                }
                $UserFollowingData[] = $arr['User'];
            }
            /*             * ***** check if next page exist or not ********* */
            $has_next = $this->request->params['paging']['UserFollower']['nextPage'];
            $this->apiOutputArr['has_next'] = $has_next;
            /*             * ***** check next page ends ********* */
        }
        $this->apiOutputArr['userFollowing'] = $UserFollowingData;
        $this->apiResponseCode = API_CODE_SUCCESS;
    }

    /**
     * set follow / unfollow status
     *
     * /users/set_follow_unfollow/{access_key}/{user_key}
     *
     * @param string $accessKey access key for access data
     * @param string $userKey   user key for access data
     *
     * @return return get following
     */
    public function set_follow_unfollow($accessKey = null, $userKey = null, $following_id = null) {
        $validationErrors = false;
        if (empty($following_id)) {
            $this->apiErrors[] = 'following id is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $UserFollower = $this->UserFollower->find('first', array('conditions' => array('UserFollower.following_id' => $following_id, 'UserFollower.follower_id' => $this->currentUser['id'])));
            if (!empty($UserFollower)) {
                $this->UserFollower->deleteAll(array('UserFollower.following_id' => $following_id, 'UserFollower.follower_id' => $this->currentUser['id']));
            } else {
                $saveDetail = array();
                $saveDetail['UserFollower']['following_id'] = $following_id;
                $saveDetail['UserFollower']['follower_id'] = $this->currentUser['id'];

                $this->UserFollower->create();
                $this->UserFollower->set($saveDetail);
                $this->UserFollower->save($saveDetail);
            }
            $UserFollowing = $this->UserFollower->find('count', array('conditions' => array('UserFollower.follower_id' => $following_id)));
            $UserFollower = $this->UserFollower->find('count', array('conditions' => array('UserFollower.following_id' => $following_id)));
            $followDetail['followers_count'] = $this->formatWithSuffix($UserFollower);
            $followDetail['following_count'] = $this->formatWithSuffix($UserFollowing);

            $this->apiOutputArr['followDetail'] = $followDetail;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    /**
     * get user acitivities
     *
     * /users/get_activity_feed/{access_key}/{user_key}/user_id/page
     *
     * @param string $accessKey access key for access data
     * @param string $userKey   user key for access data
     *
     * @return return get user activities messages
     */
    public function get_activity_feed($accessKey = null, $userKey = null, $user_id = null, $page = null) {
        $validationErrors = false;
        if (empty($user_id)) {
            $this->apiErrors[] = 'user id is missing.';
            $validationErrors = true;
        }
        if (empty($page)) {
            $this->apiErrors[] = 'page is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $userListArr = array();
            $userListArr[] = $user_id;
            $userContactArr = array();
            $UserFollowerArr = array();
            $activityFeedData = array();
            $UserPollArr = array();
            $contactUserIds = array();
            $userContactArr = $this->Contact->find('list', array('conditions' => array('Contact.from_user_id' => $user_id, 'Contact.request_status' => 1), 'fields' => 'Contact.to_user_id'));
            if (!empty($userContactArr)) {
                foreach ($userContactArr as $userContactKey => $userContactValue) {
                    $userListArr[] = $userContactValue;
                    $contactUserIds[] = $userContactValue;
                }
            }
            $userContactArr2 = $this->Contact->find('list', array('conditions' => array('Contact.to_user_id' => $user_id, 'Contact.request_status' => 1), 'fields' => 'Contact.from_user_id'));
            if (!empty($userContactArr2)) {
                foreach ($userContactArr2 as $userContactKey2 => $userContactValue2) {
                    $userListArr[] = $userContactValue2;
                    $contactUserIds[] = $userContactValue2;
                }
            }
            $UserFollowerArr = $this->UserFollower->find('list', array('conditions' => array('UserFollower.follower_id' => $user_id), 'fields' => 'UserFollower.following_id'));
            if (!empty($UserFollowerArr)) {
                foreach ($UserFollowerArr as $UserFollowerKey => $UserFollowerValue) {
                    $userListArr[] = $UserFollowerValue;
                }
            }
            $userListArr = array_unique($userListArr);
            if (count($userListArr) > 0) {
                $userFeedCondition = array(
                    'OR' => array(
                        array('ActivityFeed.feed_user_id' => $userListArr),
                        array('ActivityFeed.wallpaper_owner_user_id' => $user_id)
                    ),
                    array('OR' => array(
                            array('ActivityFeed.action' => 'comment_wallpaper', array('OR' => array('ActivityFeed.feed_user_id' => $user_id, 'ActivityFeed.wallpaper_owner_user_id' => $user_id))),
                            array('ActivityFeed.action' => 'like_wallpaper', array('OR' => array('ActivityFeed.feed_user_id' => $user_id, 'ActivityFeed.wallpaper_owner_user_id' => $user_id))),
                            array('ActivityFeed.action' => 'rate_wallpaper', array('OR' => array('ActivityFeed.feed_user_id' => $user_id, 'ActivityFeed.wallpaper_owner_user_id' => $user_id))),
                            array('ActivityFeed.action' => 'create_wallpaper')
                        )
                    ),
                    'DATE(ActivityFeed.created)' => date('Y-m-d')
                );
                $this->paginate = array('conditions' => $userFeedCondition,
                    'fields' => array('ActivityFeed.*', 'User.show_full_name', 'User.user_name', 'User.full_name', 'User.avatar', 'User.id'),
                    'limit' => 20,
                    'order' => array('ActivityFeed.created' => 'desc'),
                    'joins' => array(
                        array(
                            'table' => 'users',
                            'alias' => 'User',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'ActivityFeed.feed_user_id = User.id'
                            )
                        )
                    ),
                    'page' => $page);
                $this->ActivityFeed->recursive = 0;
                $activityFeed = array();
                try {
                    $activityFeed = $this->paginate('ActivityFeed');
                    $this->apiOutputArr['activityFeeds'] = $activityFeed;
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } catch (Exception $e) {
                    $activityFeed = array();
                }
                foreach ($activityFeed as $key => $arr) {
                    $activityFeedDataArr = array();
                    $activityFeedDataArr['user_id'] = isset($arr['ActivityFeed']['feed_user_id']) ? $arr['ActivityFeed']['feed_user_id'] : "";
                    if ($this->currentUser['id'] == $arr['ActivityFeed']['feed_user_id']) {
                        $activityFeedDataArr['user_name'] = 'you';
                        $activityFeedDataArr['full_name'] = 'you';
                    } else {
                        if (empty($arr['User']['show_full_name']) && !in_array($arr['User']['id'], $contactUserIds)) {
                            //is not friend and showfullname is off
                            $activityFeedDataArr['full_name'] = isset($arr['User']['user_name']) ? $arr['User']['user_name'] : "";
                        } else {
                            $activityFeedDataArr['full_name'] = isset($arr['User']['full_name']) ? $arr['User']['full_name'] : "";
                        }
                        $activityFeedDataArr['user_name'] = isset($arr['User']['user_name']) ? $arr['User']['user_name'] : "";
                    }
                    $activityFeedDataArr['avatar'] = isset($arr['User']['avatar']) ? $arr['User']['avatar'] : "";
                    $activityFeedDataArr['wallpaper_id'] = isset($arr['User']['wallpaper_id']) ? $arr['ActivityFeed']['wallpaper_id'] : "";
                    $activityFeedDataArr['wallpaper_title'] = isset($arr['ActivityFeed']['wallpaper_title']) ? $arr['ActivityFeed']['wallpaper_title'] : "";
                    $activityFeedDataArr['action'] = isset($arr['ActivityFeed']['action']) ? $arr['ActivityFeed']['action'] : "";
                    $activityFeedDataArr['created'] = isset($arr['ActivityFeed']['created']) ? $arr['ActivityFeed']['created'] : "";
                    $activityFeedData[] = $activityFeedDataArr;
                }
            }
            /*             * ***** check if next page exist or not ********* */
            $has_next = $this->request->params['paging']['ActivityFeed']['nextPage'];
            $this->apiOutputArr['has_next'] = $has_next;
            $this->apiOutputArr['activityFeeds'] = $activityFeedData;
            $this->apiResponseCode = API_CODE_SUCCESS;
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    /**
     * remove friend to network
     *
     * /users/removeFromFriendToNetwork/{access_key}/{user_key}/id
     *
     * @param string $accessKey access key for access data and id(contact table primary key) as post parameter
     * @param string $userKey   user key for access data
     *
     * @return response success or false
     */
    public function get_news_detail($accessKey = null, $userKey = null, $id = null) {
        $validationErrors = false;
        if (empty($id)) {
            $this->apiErrors[] = 'Id is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $newsData = $this->NewsFeed->find('first', array('conditions' => array('NewsFeed.id' => $id)));
            if (!empty($newsData)) {
                $this->apiOutputArr['newsFeed'] = $newsData['NewsFeed'];
                $this->apiResponseCode = API_CODE_SUCCESS;
            } else {
                $this->apiResponseCode = API_CODE_FAIL;
                $this->apiErrors[] = 'Record not found.';
            }
        } else {
            $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
        }
    }

    /**
     * remove friend to network
     *
     * /users/removeFromFriendToNetwork/{access_key}/{user_key}/id
     *
     * @param string $accessKey access key for access data and id(contact table primary key) as post parameter
     * @param string $userKey   user key for access data
     *
     * @return response success or false
     */
    public function removeFromFriendToNetwork($accessKey = null, $userKey = null, $id = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            if (!empty($userKey)) {
                $validationErrors = false;
                if (empty($id)) {
                    $this->apiErrors[] = 'Id is missing.';
                    $validationErrors = true;
                }
                if (!$validationErrors) {
                    $userRequestDataArr = $this->Contact->find('first', array('conditions' => array('Contact.id' => $id, 'Contact.request_status' => 1, 'Contact.type' => 'C')));
                    if (!empty($userRequestDataArr)) {
                        if ($userRequestDataArr['Contact']['user_id'] == $this->currentUser['id']) {
                            $this->Contact->id = $userRequestDataArr['Contact']['id'];
                            $Contact['Contact']['request_status'] = 0;
                            $Contact['Contact']['type'] = 'N';
                            $this->Contact->save($Contact);
                        } else if ($userRequestDataArr['Contact']['to_user_id'] == $this->currentUser['id']) {
                            $this->Contact->id = $userRequestDataArr['Contact']['id'];
                            $this->Contact->delete();
                            $newNetworkContact['Contact']['user_id'] = $this->currentUser['id'];
                            $newNetworkContact['Contact']['to_user_id'] = $userRequestDataArr['Contact']['user_id'];
                            $UserDetail = $this->User->find('first', array('conditions' => array('User.id' => $userRequestDataArr['Contact']['user_id']), 'fields' => array('User.email', 'User.full_name')));
                            $newNetworkContact['Contact']['full_name'] = $UserDetail['User']['full_name'];
                            $newNetworkContact['Contact']['email'] = $UserDetail['User']['email'];
                            $newNetworkContact['Contact']['request_status'] = 0;
                            $newNetworkContact['Contact']['type'] = 'N';
                            $this->Contact->id = null;
                            $this->Contact->save($newNetworkContact);
                        } else {
                            $this->apiErrors[] = 'No action required.';
                        }
                        $this->apiResponseCode = API_CODE_SUCCESS;
                    } else {
                        $this->apiResponseCode = API_CODE_FAIL;
                        $this->apiErrors[] = 'Contact not found.';
                    }
                } else {
                    $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
                }
            } else {
                $this->apiResponseCode = API_CODE_INVALID_USER_KEY;
            }
        }
    }

    /**
     * API method to get user_name suggession.
     * This method requires POST data.
     *
     * API URL: /users/user_name_suggest/{ACCESS_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function user_name_suggest($accessKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            $suggestionCount = 0;
            $suggestionArr = array();
            $validationErrors = false;
            if (empty($this->request->data['full_name'])) {
                $this->apiErrors[] = 'Missing full_name.';
                $validationErrors = true;
            } else {
                $full_name = $this->request->data['full_name'];
                $full_name = preg_replace('/[^A-Za-z0-9() -]/', '', $full_name);
                $usernameArr = explode(" ", $full_name);
                if (count($usernameArr) > 1) {
                    $usernameString = $usernameArr[0] . $usernameArr[1];
                    if (((strlen($usernameArr[0]) + strlen($usernameArr[1])) > USERNAME_MIN_LENGTH) && (strlen($usernameArr[0]) + strlen($usernameArr[1])) < USERNAME_MAX_LENGTH) {
                        $usernameCount = $this->User->find('count', array('conditions' => array('User.user_name' => $usernameArr[0] . $usernameArr[1])));
                        if ($usernameCount == 0) {
                            $suggestionArr[] = $usernameArr[0] . $usernameArr[1];
                        }

                        if (!in_array($usernameArr[1] . $usernameArr[0], $suggestionArr)) {
                            $usernameCount = $this->User->find('count', array('conditions' => array('User.user_name' => $usernameArr[1] . $usernameArr[0])));
                            if ($usernameCount == 0) {
                                $suggestionArr[] = $usernameArr[1] . $usernameArr[0];
                            }
                        }
                    }
                } else {
                    $usernameString = $usernameArr[0];
                }
            }

            if (empty($this->request->data['email'])) {
                $this->apiErrors[] = 'Email is missing.';
                $validationErrors = true;
            } else {
                /* filter email - make it alphanumeric */
                $email = $this->request->data['email'];
                $emailExplode = explode("@", $email);
                $emailName = $emailExplode[0];
                $domainExpolode = explode(".", $emailExplode[1]);
                $emailDomain = $domainExpolode[0];
                $usernameStr = $emailName . $emailDomain;
                $email = preg_replace('/[^A-Za-z0-9() -]/', '', $usernameStr);
                $usernameStringEmail = str_replace(" ", "", $email);
                if ((strlen($usernameStringEmail) > USERNAME_MIN_LENGTH) && (strlen($usernameStringEmail) < USERNAME_MAX_LENGTH)) {
                    if (!in_array($usernameStringEmail, $suggestionArr)) {
                        $usernameCount = $this->User->find('count', array('conditions' => array('User.user_name' => $usernameStringEmail)));
                        if ($usernameCount == 0) {
                            $suggestionArr[] = $usernameStringEmail;
                        }
                    }
                }
            }
            if (!$validationErrors) {
                // filter full name - make it alphanumeric
                if (strlen($usernameString) > 18) {
                    $usernameString = substr($usernameString, 0, 18);
                }

                while (count($suggestionArr) < USERNAME_MIN_LENGTH) {
                    if (strlen($usernameString) > 4) {
                        $usernamesuggestion = $usernameString . rand(0, 99);
                    } else if (strlen($usernameString) > 3) {
                        $usernamesuggestion = $usernameString . rand(1, 99);
                    } else if ((strlen($usernameString) > 2)) {
                        $usernamesuggestion = $usernameString . rand(10, 99);
                    } else {
                        $usernamesuggestion = $usernameString . rand(100, 999);
                    }

                    $usernameCount = $this->User->find('count', array('conditions' => array('User.user_name' => $usernamesuggestion)));
                    if ($usernameCount == 0) {
                        $suggestionArr[] = $usernamesuggestion;
                        $suggestionCount++;
                    }
                }
                $this->apiOutputArr['userNames'] = $suggestionArr;
                $this->apiResponseCode = API_CODE_SUCCESS;
            } else {
                $this->apiResponseCode = API_CODE_DATA_VALIDATION_ERROR;
            }
        }
    }

    /**
     * API URL: /users/suggestContact/{access_key}/{user_key}
     *
     * @param string $full_name
     *
     * @return return list of full name
     *
     * @author Nikhil
     */
    public function suggestContact($accessKey = null, $userKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            $validationErrors = true;
            if (!empty($this->request->data['user_name'])) {
                $validationErrors = false;
            }
            if (!$validationErrors) {
                $contactUserIdArr = array($this->currentUser['id']);
                $contactUserIdArr1 = $this->Contact->find('list', array('conditions' => array('Contact.to_user_id' => $this->currentUser['id'],
                        "OR" => array('Contact.show_in_both_network' => 1, array('Contact.type' => 'C', 'Contact.request_status' => array(0, 1)))), 'fields' => 'Contact.user_id'));
                if (!empty($contactUserIdArr1)) {
                    $contactUserIdArr = array_merge($contactUserIdArr, $contactUserIdArr1);
                }
                $contactUserArr2 = $this->Contact->find('list', array('conditions' => array('Contact.user_id' => $this->currentUser['id'], 'Contact.to_user_id NOT' => 0), 'fields' => 'Contact.to_user_id'));
                if (!empty($contactUserArr2)) {
                    $contactUserIdArr = array_merge($contactUserIdArr, $contactUserArr2);
                }
                $suggessionData = array();
                if (!empty($this->request->data['user_name'])) {
                    $suggessionData = $this->User->find('all', array(
                        'conditions' => array(
                            'User.id NOT' => $contactUserIdArr,
                            'OR' => array(
                                'User.full_name LIKE' => "%" . $this->request->data['user_name'] . "%",
                                'User.user_name LIKE' => "%" . $this->request->data['user_name'] . "%"
                            ),
                            'User.is_verified NOT' => 2
                        ),
                        'fields' => array(
                            'User.id',
                            'User.full_name',
                            'User.avatar',
                            'User.email',
                            'User.user_name'
                        )
                    ));
                }
                $finalSuggessionData = array();
                foreach ($suggessionData as $suggessionKey => $suggessionValue) {
                    $finalSuggessionData[] = $suggessionValue['User'];
                }
                $this->apiOutputArr['suggestion_list'] = $finalSuggessionData;
                $this->apiResponseCode = API_CODE_SUCCESS;
            } else {
                $this->apiErrors[] = 'No Data Passed.';
                $this->apiResponseCode = API_CODE_FAIL;
            }
        }
    }

    /**
     * API method to get requests
     * This method requires POST data.
     *
     * API URL: /users/sendVerificationMail/{access_key}/{user_key}
     *
     * @param string $accessKey access key for access data
     *
     * @return return bool
     *
     * @author Ujash
     */
    public function sendVerificationMail($accessKey = null, $userKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            $user = $this->User->find('first', array('conditions' => array('User.id' => $this->currentUser['id'])));
            if (!empty($user)) {
                if (($user['User']['is_verified'] == 0) && !empty($user['User']['verification_code'])) {

                    $templateDetail = $this->Template->find('first', array('conditions' => array('Template.key' => 'verify_email'),
                        'fields' => array('subject', 'content', 'tags')));
                    $arr1 = explode(',', $templateDetail['Template']['tags']);
                    $verificationLink = '<a style="color:#F68B1F" href="' . WEB_URL . 'users/validate_account/' . $user['User']['verification_code'] . '">' . WEB_URL . 'validate_account</a>';
                    $arr2 = array($user['User']['full_name'], $verificationLink);
                    $emailData = str_replace($arr1, $arr2, $templateDetail['Template']['content']);
                    try {
                        if (strpos($user['User']['email'], 'example') == false) {
                            if (EMAIL_SETTING != "mendrillAPI") {
                                $this->send_email($templateDetail['Template']['subject'], $emailData, $user['User']['email'], 'mail_temp');
                            } else {
                                $emailData = $this->addHdrFtr($emailData);
                                $this->mandrillEmailToArr[] = array("email" => $user['User']['email'], "name" => $user['User']['full_name'], "type" => 'to');
                                $varArr = array("name" => "EMAIL", "content" => $emailData);
                                $this->mandrillEmailVarsArr[] = array("rcpt" => $user['User']['email'], "vars" => array($varArr));
                                $this->mandrill_api_email($templateDetail['Template']['subject'], $this->mandrillEmailToArr, $this->mandrillEmailVarsArr);
                            }
                        }
                    } catch (Exception $e) {
                        
                    }
                    $this->apiResponseCode = API_CODE_SUCCESS;
                } else if ($user['User']['is_verified'] == 1) {
                    $this->apiResponseCode = API_CODE_FAIL;
                    $this->errorsToReturn[] = 'Already verified.';
                } else {
                    $this->apiErrors[] = 'Something wrong.';
                    $this->apiResponseCode = API_CODE_FAIL;
                }
            } else {
                $this->apiResponseCode = API_CODE_FAIL;
                $this->apiErrors[] = 'No user found.';
            }
        }
    }

    /**
     * API method to get interest list.
     * This method requires POST data.
     *
     * API URL: /users/getInterestList/{ACCESS_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function getInterestList($accessKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            // filter full name - make it alphanumeric
            $interestListArr = $this->Interest->find('all', array('fields' => array('Interest.id', 'Interest.parent_id', 'Interest.name'),
                'order' => array('Interest.name' => 'ASC')));
            foreach ($interestListArr as $interestList) {
                foreach ($interestList as $interestListsub) {
                    $interest_list[] = $interestListsub;
                }
            }
            $this->apiOutputArr['interest_list'] = $interest_list;
            $this->apiResponseCode = API_CODE_SUCCESS;
        }
    }

    /**
     * API method to get country list.
     * This method requires POST data.
     *
     * API URL: /users/get_country_list/{ACCESS_KEY}
     *
     * @param string $accessKey access token for access data
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function get_country_list($accessKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            // filter full name - make it alphanumeric
            $countryList = $this->Country->find('list', array('fields' => array('Country.id', 'Country.name'), 'order' => 'name'));
            if (($key = array_search('United States', $countryList)) !== false) {
                unset($countryList[$key]);
            }
            array_unshift($countryList, 'United States');
            $this->apiOutputArr['countryList'] = array_values($countryList);
            $this->apiResponseCode = API_CODE_SUCCESS;
        }
    }

    /**
     * API method to get country list.
     * This method requires POST data.
     *
     * API URL: /users/get_all_user_list/{ACCESS_KEY}
     *
     */
    public function get_all_user_list($accessKey = null, $userKey = null, $page = null) {
        $validationErrors = false;
        if (empty($page)) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $condition = array('User.id NOT' => $this->currentUser['id']);
            self::generate_all_user_list($condition, $page);
            // it will also return the result
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function search_all_user_list($accessKey = null, $userKey = null, $page = null) {
        $validationErrors = false;
        if (empty($page)) {
            $this->apiErrors[] = 'Page number is missing.';
            $validationErrors = true;
        }
        if (empty($this->request->data['search_name'])) {
            $this->apiErrors[] = 'search_name is missing.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            $condition = array(
                'User.id NOT' => $this->currentUser['id'],
                "OR" => array(
                    'User.user_name Like' => "%" . $this->request->data['search_name'] . "%",
                    'User.full_name Like' => "%" . $this->request->data['search_name'] . "%"
                )
            );
            self::generate_all_user_list($condition, $page);
            // it will also return the result
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    private function generate_all_user_list($condition = null, $page = null) {
        $userDataArr = array();
        $this->paginate = array(
            'conditions' => $condition,
            'joins' => array(
                array(
                    'table' => 'contacts',
                    'alias' => 'Contact',
                    'type' => 'LEFT',
                    'conditions' => array(
                        "OR" => array(
                            array('User.id = Contact.to_user_id', 'Contact.from_user_id=' . $this->currentUser['id']),
                            array('User.id = Contact.from_user_id', 'Contact.to_user_id=' . $this->currentUser['id'])
                        )
                    ),
                )
            ),
            'fields' => array('User.id', 'User.full_name', 'User.avatar', 'User.user_name', 'User.show_full_name', 'User.email', 'Contact.*'),
            'order' => array('User.full_name' => 'ASC'),
            'limit' => HIEGHER_PAGINATION_LIMIT,
            'page' => $page
        );
        try {
            $userDataArr = $this->paginate('User');
        } catch (Exception $e) {
            $userDataArr = array();
        }
        $finalUserData = array();
        $finalUserArr = array();
        if (!empty($userDataArr)) {
            foreach ($userDataArr as $mixData) {
                $finalUserData['User'] = $mixData['User'];
                if (!empty($mixData['Contact']['id'])) {
                    if ($mixData['Contact']['type'] == "C") {
                        if ($mixData['Contact']['request_status'] == 1) {
                            $finalUserData['User']['friend_relation'] = "FRND";
                        } else {
                            if ($mixData['Contact']['from_user_id'] == $this->currentUser['id']) {
                                $finalUserData['User']['friend_relation'] = "RQSTED";
                            } else {
                                $finalUserData['User']['friend_relation'] = "GOT_RQSTED";
                            }
                            if (empty($finalUserData['User']['show_full_name'])) {
                                $finalUserData['full_name'] = isset($finalUserData['User']['user_name']) ? $finalUserData['User']['user_name'] : "";
                                $finalUserData['user_name'] = "";
                            }
                        }
                    } else {
                        if (empty($finalUserData['User']['show_full_name'])) {
                            $finalUserData['full_name'] = isset($finalUserData['User']['user_name']) ? $finalUserData['User']['user_name'] : "";
                            $finalUserData['user_name'] = "";
                        }
                        if ($mixData['Contact']['from_user_id'] == $this->currentUser['id']) {
                            $finalUserData['User']['friend_relation'] = "NTWRK";
                        } else if ($mixData['Contact']['show_in_both_network'] == 1) {
                            $finalUserData['User']['friend_relation'] = "NTWRK";
                        } else {
                            $finalUserData['User']['friend_relation'] = "NO";
                        }
                    }
                    $finalUserData['User']['contact_table_id'] = $mixData['Contact']['id'];
                } else {
                    $finalUserData['User']['friend_relation'] = "NO";
                }
                unset($finalUserData['User']['show_full_name']);
                $finalUserArr[] = $finalUserData['User'];
            }
            /*             * ***** check if next page exist or not ********* */
            $has_next = $this->request->params['paging']['User']['nextPage'];
            $this->apiOutputArr['has_next'] = $has_next;
            /*             * ***** check next page ends ********* */
        }
        $this->apiOutputArr['User'] = $finalUserArr;
        $this->apiResponseCode = API_CODE_SUCCESS;
    }

    public function change_password($accessKey = null, $userKey = null) {
        $validationErrors = false;
        $getUser = $this->User->findById($this->currentUser['id']);
        if (md5($this->request->data['old_password']) != $getUser['User']['password']) {
            $this->errorsToReturn[] = 'Old password does not match.';
            $validationErrors = true;
        }
        if (empty($this->request->data['new_password'])) {
            $this->apiErrors[] = 'New password is missing.';
            $validationErrors = true;
        }
        if (empty($this->request->data['repeat_password'])) {
            $this->apiErrors[] = 'Repeat password is missing.';
            $validationErrors = true;
        }
        if ($this->request->data['repeat_password'] != $this->request->data['new_password']) {
            $this->errorsToReturn[] = 'Password does not match with repeat password.';
            $validationErrors = true;
        }
        if (!empty($this->request->data['new_password'])) {
            $this->request->data['password'] = md5($this->request->data['new_password']);
        }

        if (!$validationErrors) {
            $this->request->data['id'] = $this->currentUser['id'];
            $this->User->set($this->request->data);
            $result = $this->User->save($this->request->data);
            if ($result) {
                $this->apiResponseCode = API_CODE_SUCCESS;
            } else {
                $this->apiErrors[] = 'Fail to update password.';
                $this->apiResponseCode = API_CODE_FAIL;
            }
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    public function global_setting($accessKey = null, $userKey = null) {
        $validationErrors = false;
        $globalData = $this->User->find('first', array(
            'conditions' => array('User.id' => $this->currentUser['id']),
            'fields' => array('User.allow_notification', 'User.profile_visibility', 'User.like_notification', 'User.comment_notification', 'allow_following_detail')
        ));
        if (empty($globalData)) {
            $this->apiErrors[] = 'User not found.';
            $validationErrors = true;
        }
        if (!$validationErrors) {
            //************ if post parameter are set -> apply set logic
            if (isset($this->request->data['allow_notification']) || isset($this->request->data['profile_visibility']) || isset($this->request->data['like_notification']) || isset($this->request->data['comment_notification'])) {
                if (!in_array($this->request->data['allow_notification'], array(1, 0))) {
                    $this->apiErrors[] = 'Value of allow_notification must be 0/1.';
                    $validationErrors = true;
                }
                if (!in_array($this->request->data['like_notification'], array(1, 0))) {
                    $this->apiErrors[] = 'Value of like_notification must be 0/1.';
                    $validationErrors = true;
                }
                if (!in_array($this->request->data['comment_notification'], array(1, 0))) {
                    $this->apiErrors[] = 'Value of comment_notification must be 0/1.';
                    $validationErrors = true;
                }
                if (!in_array($this->request->data['allow_following_detail'], array(1, 0))) {
                    $this->apiErrors[] = 'Value of allow_following_detail must be 0/1.';
                    $validationErrors = true;
                }
                if (!in_array($this->request->data['profile_visibility'], array("PUBLIC", "PRIVATE"))) {
                    $this->apiErrors[] = 'Value of profile visibility is not correct.';
                    $validationErrors = true;
                }
                if (!$validationErrors) {
                    $globalData['User']['id'] = $this->currentUser['id'];
                    if (isset($this->request->data['allow_notification'])) {
                        $globalData['User']['allow_notification'] = $this->request->data['allow_notification'];
                    }
                    if (isset($this->request->data['profile_visibility'])) {
                        $globalData['User']['profile_visibility'] = $this->request->data['profile_visibility'];
                    }
                    if (isset($this->request->data['show_full_name'])) {
                        $globalData['User']['show_full_name'] = $this->request->data['show_full_name'];
                    }
                    if (isset($this->request->data['like_notification'])) {
                        $globalData['User']['like_notification'] = $this->request->data['like_notification'];
                    }
                    if (isset($this->request->data['comment_notification'])) {
                        $globalData['User']['comment_notification'] = $this->request->data['comment_notification'];
                    }
                    if (isset($this->request->data['allow_following_detail'])) {
                        $globalData['User']['allow_following_detail'] = $this->request->data['allow_following_detail'];
                    }
                    $result = $this->User->save($globalData);
                    if ($result) {
                        $this->apiOutputArr['global_setting'] = $globalData['User'];
                        $this->apiResponseCode = API_CODE_SUCCESS;
                    } else {
                        $this->apiErrors[] = 'Fail to update!';
                        $this->apiResponseCode = API_CODE_FAIL;
                    }
                } else {
                    $this->apiResponseCode = API_CODE_FAIL;
                }
            } else {//*********** if post parameter are not set -> return detail
                $this->apiOutputArr['global_setting'] = $globalData['User'];
                $this->apiResponseCode = API_CODE_SUCCESS;
            }
        } else {
            $this->apiResponseCode = API_CODE_FAIL;
        }
    }

    /* sends test notification to all active devices */

    public function send_test_notificaiton($accessKey = null) {
        if (empty($accessKey)) {
            $this->apiResponseCode = API_CODE_MISSING_ACCESS_KEY;
        } else {
            // filter full name - make it alphanumeric
            $this->UserDevice->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id'
                    )
                )
                    ), false);
            $getUserDevicesArr = $this->UserDevice->find('all', array('fields' => array('UserDevice.id', 'UserDevice.device_token'), 'conditions' => array('UserDevice.is_login' => 1, 'User.email LIKE' => "%multidots%")));
            $getUserDevices = array();
            if (!empty($getUserDevicesArr)) {
                foreach ($getUserDevicesArr as $key => $value) {
                    $getUserDevices[] = $value['UserDevice']['device_token'];
                }
            }
            if (!empty($getUserDevices)) {
                $title = 'Test notification';
                $msg = "this is test notification so ignore it.";
                try {
                    foreach ($getUserDevices as $deviceToken) {
                        if (!empty($deviceToken)) {
                            $this->sendIphoneNotification($deviceToken, $title, $msg, 0, 0, 0, null, null);
                        }
                    }
                } catch (Exception $e) {
                    
                }
            }
        }
    }

    /**
     * add to friend
     *
     * API URL: /friend_request/{code}
     *
     * @param string $code 
     *
     * @return open app if mobile else redirect to different URLs.
     */
    public function friend_request_schema($code = null) {
        if (!empty($code)) {
            $code = $this->decrypt($code, ENCRYPT_DECRYPT_KEY);
            $user_id = explode('-', $code);
            $device = $this->deviceType();
            if ($device == 'android') {
                ?>
                <script type="text/javascript">
                    //<![CDATA[
                    setTimeout(function () {
                        window.location = "https://play.google.com/store/apps/details?id=bluefusion.selfie.socialrating";
                    }, 25);
                    window.location = "bluefusion.selfie.socialrating://?user_id=<?php echo $user_id[0]; ?>";
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
                    window.location = 'Wallpaper://?user_id=<?php echo $user_id[0]; ?>';
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

    public function index() {
        try {
            $this->paginate = array('limit' => NORMAL_PAGINATION_LIMIT, 'order' => array('full_name' => 'asc'));
            $this->User->recursive = 0;
            $this->set('usersArr', $this->paginate('User'));
        } catch (NotFoundException $e) {
            $this->Session->setFlash('Please check, Something is wrong.', 'flashError');
            $this->redirect("/users");
        }
        $this->set('title_for_layout', 'Users');
    }

}
