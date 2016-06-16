<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('MissingAccessKeyException', 'Error/Exception');
App::uses('EmailErrorHandler', 'Error');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link           http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 * @throws
 */
class AppController extends Controller {
    /* Components
     * @var array
     * @throws
     */

    public $components = array('Common', 'Session', 'RequestHandler', 'Email', 'RememberMe', 'Breadcrumb', 'MDImage');

    public $helpers = array('Common', 'Breadcrumb', 'FormValidation', 'Form');
    /*
     * Models
     * @var array
     */
    public $uses = array('ApiKey', 'ApiRequest', 'User', 'WallpaperRequest');

    /*
     * web controllers And web actions
     * @var array
     */
    public $webControllers = array('users', 'wallpapers', 'comments');

    /*
     * Default Layout
     * @var String
     */
    public $layout = 'admin';

    /*
     * Default Type (api or admin)
     * @var String
     */
    public $type = '';

    /*
     * Hold request data
     * @var array
     */
    public $apiRequestData = array();
    public $apiRequestUpdateId = "";

    /*
     * Stores predefined status codes
     * @var array
     */
    public $apiResponseCodesArr = array();

    /*
     * Hold a response code for running api call
     * @var string
     */
    public $apiResponseCode = null;

    /*
     * Stores the final output of api call
     * Used in view file json decoded format
     * @var array
     */
    public $apiOutputArr = array();

    /*
     * Holds the current api user data
     *
     * @var array
     */
    public $currentUser = null;

    /*
     * Secure controllers
     *
     * @var array
     */
    public $authEnabledControllers = array('Users', 'Wallpaperes', 'SpamReports');

    /*
     * Store error messages
     * @var array
     */
    public $apiErrors = array();

    /*
     * Store conditional messages
     * @var array
     */
    public $errorsToReturn = array();

    /**
     * beforeFilter
     *
     * @throws MissingAccessKeyException
     *
     * @return mixed
     */
    public function beforeFilter() {
        $this->__checkIfFrontWebPage();
        $this->__setTypeForOutput();
        if ($this->type == 'api') {
            $this->__generateApiResponseCodes();
            $this->__prepareRequestData();
            $this->__validateApiAccess();
            $this->__setEmailSettings();
            parent::beforeFilter();
        } else {
            parent::beforeFilter();
            if ($this->request->params['controller'] != 'pages') {
                $this->__validateAndSetCurrentAdmin();
                $this->__setEmailSettings();
            }
        }
        $this->Session->write('Config.siteLanguages', array('en_GB' => 'English', 'it' => 'Other'));
    }

    /**
     * Validates the user
     *
     * It checks whether user is logged in or not and redirects to corresponding page
     * Also it sets a global variable with loggedin user data which can be
     * used in other controllers and views
     *
     * @author The Chief
     * @return mixed
     */
    private function __validateAndSetCurrentAdmin() {
        $loggedinAdmin = $this->Session->read('Config.LoggedinAdmin');
        if ($loggedinAdmin) {
            $this->loggedinAdmin = $loggedinAdmin;
            $img = $this->Admin->find('first', array('conditions' => array('Admin.name' => $loggedinAdmin['name'])));
            $this->set('adminimg', $img);
            $this->set('loggedinAdmin', $this->loggedinAdmin);
            if ('login' == $this->request->params['action'] || 'forgotpassword' == $this->request->params['action']) {
                $this->redirect('/');
            }
        } else {
            $isRemembered = $this->RememberMe->getRememberedUser();
            if (!empty($isRemembered) && 'logout' != $this->request->params['action']) {
                $admin = $this->Admin->find('first', array('conditions' => array('Admin.email' => $isRemembered)));
                if (!empty($admin)) {
                    $this->RememberMe->removeRememberedUser();
                    $this->RememberMe->rememberUser($isRemembered);
                    $this->setAdminSession($admin['Admin']);
                    $this->Admin->id = $admin['Admin']['id'];
                    $this->Admin->saveField('last_login', date('Y-m-d H:i:s'));
                    $this->setAdminPreferences(true);
                    $this->Session->setFlash('Welcome back ' . $admin['Admin']['name'] . '!', 'flashSuccess');
                    $this->redirect('/');
                }
            }
            if ('login' != $this->request->params['action'] && 'forgotpassword' != $this->request->params['action'] && 'register' != $this->request->params['action'] && 'forgot_password_reset' != $this->request->params['action'] && 'validate_account' != $this->request->params['action']) {
                $this->redirect('/admins/login');
            }
        }
    }

    /**
     * Sets the current user session
     *
     * @author The Chief
     *
     * @param array $userArr Data array of the user
     */
    public function setUserSession($userArr = array()) {
        if (!empty($userArr)) {
            if ($this->Session->read('Config.LoggedinUser')) {
                $this->Session->delete('Config.LoggedinUser');
            }
            $this->Session->write('Config.LoggedinUser', $userArr);
        }
    }

    /**
     * Validates the type, confirm that its api call or website call
     *
     * @access public
     *
     * @throws MissingAccessKeyException
     *
     * @return mixed
     */
    private function __setTypeForOutput() {
        if (!empty($this->request->params['type']) && 'api' == $this->request->params['type']) {
            if (!empty($this->request->params['code'])) {
                $this->layout = 'htmlResponse';
                $this->type = 'api';
            } else {
                $this->layout = 'apiJsonResponse';
                $this->type = 'api';
            }
        }
        if ($this->request->params['controller'] == 'pages') {
            $this->layout = 'blank';
        }
    }


    private function __checkIfFrontWebPage() {
//        if (SITE_URL == WEB_URL) {
//            if (in_array($this->request->params['controller'], $this->webControllers) && in_array($this->request->params['action'], $this->webViews)) {
//                
//            } else {
//                $this->redirect('http://www.google.com/');
//            }
//        }
    }

    /**
     * Sets default settings for CakeEmail component
     *
     * @return void
     */
    private function __setEmailSettings() {
        $this->cronEmails = array(
            'dev' => array(
                'nikhil.parmar@multidots.in'
            ),
            'live' => array(
                'nikhil.parmar@multidots.in'
            )
        );
    }

    /**
     * afterFilter
     * afterFilter
     *
     * @return void
     */
    public function afterFilter() {
        if ($this->type == 'api') {
            if (in_array($this->name, array('Authorize', 'Users', 'Contacts', 'Groups', 'Wallpaperes', 'Interests', 'Comments'))) {
                $this->logApiRequest();
            }
        }
        parent::afterFilter();
    }

    /**
     * beforeRender
     * beforeRender
     *
     * @return void
     */
    public function beforeRender() {
        if ($this->type == 'api') {
            if ('test' != $this->request->params['action'] && 'alertForPickupTime' != $this->request->params['action'] && 'pushNotification' != $this->request->params['action'] && 'validate_account' != $this->request->params['action'] && 'block' != $this->request->params['action'] && 'forgotPasswordReset' != $this->request->params['action'] && 'emailViewWallpaper' != $this->request->params['action'] && 'show' != $this->request->params['action'] && 'View' != $this->request->params['action']) {
                if ($this->__isApiCallError()) {
                    if (empty($this->apiResponseCodesArr)) {
                        $this->__generateApiResponseCodes();
                    }
                    if (!empty($this->viewVars['error']) && $this->viewVars['error'] instanceof MissingAccessKeyException) {
                        $this->apiResponseCode = 1101;
                        if (!empty($this->viewVars['message'])) {
                            $this->errorsToReturn[] = $this->viewVars['message'];
                        } else {
                            $this->apiResponseCode = API_CODE_INVALID_API_CALL;
                        }
                        $this->apiSuccess = 0;
                    } else {
                        $this->apiResponseCode = API_CODE_INVALID_API_CALL;
                        $this->apiSuccess = 0;
                    }
                    $this->response->statusCode(200);
                } else {
                    parent::beforeRender();
                }
                $this->__buildApiOutput();
                $this->set('ApiOutput', $this->apiOutputArr);
            } else {
                parent::beforeRender();
            }
        } else {
            parent::beforeRender();
            $this->__setErrorConfiguration();
        }
    }

    /**
     * Prepares an object with all output data
     *
     * @return void
     */
    private function __buildApiOutput() {
        if ($this->type == 'api') {
            if(empty($this->apiResponseCode)){
                $this->apiResponseCode = 1234;
            }
            $this->apiOutputArr['response_code'] = $this->apiResponseCode;
            if($this->apiResponseCode != 1111){
                if(empty($this->apiErrors) && empty($this->errorsToReturn)){
                     if(!empty($this->apiResponseCodesArr[$this->apiResponseCode])) {
                        $this->apiErrors[] = $this->apiResponseCodesArr[$this->apiResponseCode];
                    } else {
                        $this->apiErrors[]= "Something went wrong! Please try again.";
                    }
                }
            }
            //$this->apiOutputArr['api_call_time'] = date('Y-m-d H:i:s');

            // set error
            if (!empty($this->apiErrors)) {
                if (ERROR_DISABLE) {
                    $this->apiOutputArr['error'] = array("Something went wrong! Please try again.");
                    try {
                        if (UR_APP_MODE == 'live') {
                            $Email = new CakeEmail('office');
                            $Email->to('nikhil.parmar@multidots.in');
                            $Email->addTo('gautam@multidots.in');
                            $Email->addTo('paras.joshi@multidots.in');
                            $Email->addTo('rajan.kambaliya@multidots.in');
                            $Email->subject($_SERVER['HTTP_USER_AGENT'] . SITE_URL . " - Field Missing Error");
                            $Email->replyTo('notify@urateitapp.com');
                            $Email->from('notify@urateitapp.com');
                            $emailBody = Router::url($this->here, true);
                            $emailBody .= "</br>" . json_encode($this->apiErrors);
                            $Email->send($emailBody);
                        }
                    } catch (Exception $ex) {
                        
                    }
                } else {
                    $this->apiOutputArr['error'] = $this->apiErrors;
                }
            } else if (!empty($this->errorsToReturn)) {
                $this->apiOutputArr['error'] = $this->errorsToReturn;
            }
        }
    }

    /**
     * Checks for valid api access
     *
     * @return bool
     * @throws MissingAccessKeyException
     */
    private function __validateApiAccess() {
        if ($this->type == 'api') {
            if (!empty($this->request->params['code'])) {
                //for direct access to the urls
                return true;
            }
            if (!$this->__isValidAccessKey()) {
//                $error = array('response_code' => 1101, 'api_call_time' => date('Y-m-d H:i:s'), 'error' => array($this->apiErrors));
                $error = array('response_code' => 1101, 'error' => array($this->apiErrors));
                echo json_encode($error);
                exit();
            }
            if (!$this->__isValidUserKey()) {
                $error = array('response_code' => 1101, 'error' => array($this->apiErrors));
                echo json_encode($error);
                exit();
            }
            return true;
        }
    }

    /**
     * Checks if the provided ACCESS KEY is valid or not
     *
     * @return bool
     */
    private function __isValidAccessKey() {
        if ($this->type == 'api') {
            if (in_array($this->name, $this->authEnabledControllers) && $this->request->params['action'] != 'validate_account') {
                if (!empty($this->request->params['accesskey'])) {
                    $isValid = $this->ApiKey->find('count', array('conditions' => array('ApiKey.access_key' => $this->request->params['accesskey'])));
                    if ($isValid > 0) {
                        return true;
                    }
                }
            } else {
                return true;
            }
            $this->apiErrors = __('Accesskey is invalid.');
            return false;
        }
    }

    /**
     * Checks if the provided USER KEY is valid or not
     *
     * @return bool
     */
    private function __isValidUserKey() {
        if ($this->type == 'api') {
            if (in_array($this->name, $this->authEnabledControllers) && $this->request->params['action'] != 'validate_account') {
                if (in_array($this->request->params['action'], array('login', 'register', 'forgot_password'))) {
                    return true;
                } elseif (!empty($this->request->params['userkey'])) {
                    //to skip beforFind condition in next find query, set below variable to false
                    Configure::write('addBeforFindCondition', false);
                    $userFields = $this->User->getUsersFields();
                    $user = $this->User->find('first', array('conditions' => array('User.user_key' => $this->request->params['userkey']),
                        'fields' => $userFields));
                    if (!empty($user)) {
                        if ($user['User']['is_deleted'] == 0) {
                            $this->currentUser = $user['User'];
                            return true;
                        } else {
                            if($user['User']['is_deleted'] == 1){
                                $this->apiErrors = __('User is deleted.');
                            }else if($user['User']['is_deleted'] == 2){
                                $this->apiErrors = __('Blocked by Admin.');
                            }
                        }
                    } else {
                        $this->apiErrors = __('User is blocked/invalid.');
                    }
                }
            } else {
                return true;
            }
            return false;
        }
    }

    /**
     * Checks for any error in API call request
     *
     * @return bool
     */
    private function __isApiCallError() {
        if ($this->type == 'api') {
            if ('CakeError' == $this->name) {
                if (!empty($this->viewVars['error'])) {
                    $this->apiRequestData['response_error'] = print_r($this->viewVars['error'], true);
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Prepares data array of API request
     * This data array will be used in logApiRequest() function.
     *
     * @return void
     */
    private function __prepareRequestData() {
        if ($this->type == 'api') {
            if ($this->request->params['action'] != 'activatePendingWallpaper' && $this->request->params['action'] != 'GetWallpaperImageById' && $this->request->params['action'] != 'inviteUsersForWallpaper' && $this->request->params['action'] != 'inviteUsersForWallpaperMultiCurl' && $this->request->params['action'] != 'spam_data') {
                $requestData = $this->request;
                $this->apiRequestData['access_key'] = !empty($this->request->params['accesskey']) ? $this->request->params['accesskey'] : '';
                $this->apiRequestData['user_key'] = !empty($this->request->params['userkey']) ? $this->request->params['userkey'] : '';
                $this->apiRequestData['module'] = $this->request->params['controller'];
                $this->apiRequestData['method'] = $this->request->params['action'];
                $this->apiRequestData['request_url'] = $this->request->url;
                $this->apiRequestData['request_params'] = json_encode($requestData);
                $this->apiRequestData['client_ip'] = $this->RequestHandler->getClientIP();
                $this->apiRequestData['platform'] = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
                $this->apiRequestData['response_code'] = "";
                $this->apiRequestData['response_error'] = "";
                $this->ApiRequest->id = null;
                $this->ApiRequest->save($this->apiRequestData);
                $this->apiRequestUpdateId = $this->ApiRequest->getLastInsertId();
                unset($requestData);
            }
        }
    }

    /**
     * Save api call
     *
     * @return void
     */
    public function logApiRequest() {
        if ($this->type == 'api') {
            if ($this->request->params['action'] != 'activatePendingWallpaper' && $this->request->params['action'] != 'GetWallpaperImageById' && $this->request->params['action'] != 'inviteUsersForWallpaper' && $this->request->params['action'] != 'inviteUsersForWallpaperMultiCurl' && $this->request->params['action'] != 'spam_data') {
                $this->apiRequestData['response_code'] = $this->apiResponseCode;
                $this->apiRequestData['response_error'] = json_encode($this->apiOutputArr);
                $this->ApiRequest->id = $this->apiRequestUpdateId;
                $this->ApiRequest->save($this->apiRequestData);
            }
        }
    }

    /**
     * Prepares an array of API response codes with their message
     *
     * @return void
     */
    private function __generateApiResponseCodes() {
        $this->apiResponseCodesArr = array(
            API_CODE_SUCCESS => 'Success',
            API_CODE_FAIL => 'Fail',
            API_CODE_INVALID_API_CALL => 'Invalid api method.',
            API_CODE_BLANK_PUBLIC_KEY => 'Please provide your public key.',
            API_CODE_INVALID_PUBLIC_KEY => 'Public key is not valid.',
            API_CODE_PUBLIC_KEY_FAIL => 'An error occurred while generating access key. Please try again.',
            API_CODE_MISSING_ACCESS_KEY => 'Access key is invalid or missing. A valid access key is required to access this api.',
            API_CODE_NO_POST => 'You must post data to this api.',
            API_CODE_LOGIN_SUCCESS => 'You have successfully signed in.',
            API_CODE_LOGIN_SUCCESS_SOCIAL => 'You have successfully signed in with social account.',
            API_CODE_LOGIN_FAILED => 'Sign in failed!',
            API_CODE_LOGOUT_FAILED => 'Sign out failed!',
            API_CODE_REGISTER_SUCCESS => 'Registration successful.',
            API_CODE_SOCIAL_REGISTER_SUCCESS => 'Registration successful with social account.',
            API_CODE_REGISTER_FAILED => 'Registration failed.',
            API_CODE_RESET_PASSWORD_SUCCESS => 'Password reset successfully.',
            API_CODE_RESET_PASSWORD_FAILED => 'Password reset failed.',
            API_CODE_DATA_VALIDATION_ERROR => 'Data validation errors.',
            API_CODE_BLANK_UUID_KEY => 'Please provide your UUID key.',
            API_CODE_UUID_KEY_FAIL => 'An error occurred while generating access key. Please try again.',
            API_CODE_EMAIL_ERROR => 'This email already exist.',
            API_CODE_SOMETHING_WENT_WRONG_ERROR => 'Something went wrong.',
            API_CODE_VALIDATED_ACCOUNT_UPDATE_SUCCES => 'User account updated successfully. ',
            API_CODE_VALIDATED_ACCOUNT_ERROR => 'User account update failed.',
            API_CODE_INVALID_USER_KEY => 'User key is invalid or missing. A valid user key required to access this api',
            API_CODE_USER_FAILED => 'User not found',
            API_CODE_SOCIAL_USER_NOT_FOUND => 'Social user not found.',
            NO_RECORD_FOUND => 'No record found.',
            NO_GROUP_FOUND => 'No group found.',
            NO_GROUP_USER_FOUND => 'No group user found.',
            POLL_DOES_NOT_EXIST => 'Wallpaper does not exist.',
            USER_DOES_NOT_EXIST => 'User does not exist.'
        );
    }

    /**
     * Overridden function to bypass view file rendering of ajax call
     * and return type data is in json format
     *
     * @param string $view   View to use for rendering
     * @param string $layout Layout to use
     *
     * @return CakeResponse A response object containing the rendered view.
     */

    /**
     * Sets the layout to error page if any error
     *
     * @author The Chief
     * @return void
     */
    private function __setErrorConfiguration() {
        if ('CakeError' == $this->name) {
            $this->layout = 'error';
        }
        $keywordsArray = array(
            '{USER_FULLNAME}' => 'Full name of a company this email is being sent to',
            '{CONTACT_FULLNAME}' => 'Contact full name',
            '{COMPANY_NAME}' => 'Company name',
            '{CONTACT_FULLNAME_WITH_LINK}' => 'Contact full name with link',
            '{COMPANY_NAME_WITH_LINK}' => 'Company name with link',
        );
        //$this->set('keywordsArray', serialize($keywordsArray));
    }

    public function render($view = null, $layout = null) {
        if ($this->type == 'api') {
            if ('apiJsonResponse' == $this->layout) {
                $this->beforeRender();
                $this->Components->trigger('beforeRender', array(&$this));

                $viewClass = $this->viewClass;
                if ($this->viewClass != 'View') {
                    list($plugin, $viewClass) = pluginSplit($viewClass, true);
                    $viewClass = $viewClass . 'View';
                    App::uses($viewClass, $plugin . 'View');
                }

                $View = new $viewClass($this);
                $this->autoRender = false;
                $this->View = $View;

                $this->response->body($View->renderLayout("", $View->layout));
                return $this->response;
            } else {
                return parent::render($view, $layout);
            }
        } else {
            return parent::render($view, $layout);
        }
    }

    /**
     * send_email
     *
     * @param string $emailKey    emailKey
     * @param string $emailValues emailValues
     * @param string $to          to
     * @param string $template    layout of mail template
     *
     * @return CakeResponse A response object containing the rendered view.
     */
    public function send_email($emailKey = null, $emailValues = null, $to = null, $template = 'mail_temp') {
        try {
            $this->set('content', $emailValues);
            $Email = new CakeEmail('mendrill');
            $Email->to($to);
            $Email->subject($emailKey);
            $Email->replyTo(REPLY_EMAIL, REPLY_EMAIL_NAME);
            $Email->from(FROM_EMAIL, FROM_EMAIL_NAME);
            $Email->template($template);
            $Email->emailFormat('html');
            $Email->send($emailValues);
        } catch (Exception $ex) {
        }
        return true;
    }

    /*
     * this set header and footer in email content
     */

    public function addHdrFtr($emailData = null) {
        $finalData = '<html><head>
 <style>
    #border_div{
      border: 1px solid rgb(236,97,21); 
      padding: 20px; 
      border-radius: 10px;
    }
 </style>
</head>
<body style = "text-align: center;">
<table style = "display: inline-block; border-radius: 10px; border-collapse: collapse; border: 1px solid #494949;max-width: 550px">
    <tr>
        <td style= "border-radius: 6px 6px 0 0; padding: 10px 0; text-align: center; background: rgb(73, 73, 73) none repeat scroll 0% 0%;">
           <img src="' . EMAIL_HEADER_LOGO_URL . '" alt="logo" width="76px"/>
        </td>
    </tr> ' .
                $emailData .
                '<tr>
        <td style= "padding: 10px 20px; text-align: left;">
        Thank you,<br/>
        The URateIt Team
        </td>
    </tr>';
        if (!empty($this->request->data['blockUserLink'])) {
            $finalData .= '<tr><td style="color:#ccc; border-radius: 0 0 6px 6px; padding: 10px 0; text-align: center; background: rgb(73, 73, 73) none repeat scroll 0% 0%;">Don\'t want to receive wallpapers from this user? <b style="color:#F68B1F">' . $this->request->data['blockUserLink'] . '</b> Him/Her.</td></tr>';
        }
        $finalData .= '</table></body></html>';
        return $finalData;
    }

    public function mandrill_api_email($subject = null, $toArr = array(), $varArr = array()) {
        if (SANDBOXMAIL && (UR_APP_MODE == 'live')) {
            try {
                $mandrill = new Mandrill(MANDRILL_API_KEY);
                $message = array(
                    'html' => '*|EMAIL|*',
                    'subject' => $subject,
                    'from_email' => FROM_EMAIL,
                    'from_name' => FROM_EMAIL_NAME,
                    'to' => $toArr,
                    'merge' => true,
                    'merge_vars' => $varArr,
                    'headers' => array('Reply-To' => FROM_EMAIL),
                    'important' => false,
                    'return_path_domain' => FROM_EMAIL,
                );
                $async = false;
                $ip_pool = 'Main Pool';
                $result = $mandrill->messages->send($message, $async, $ip_pool);
            } catch (Mandrill_Error $e) {
                
            }
        }

        return true;
    }

    /**
     * getCurrentSiteUrl
     *
     * @return Current Url
     */
    public function getCurrentSiteUrl() {
        $pageURL = 'http';
        if (!empty($_SERVER["HTTPS"]) == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        return $url = $pageURL . $_SERVER['HTTP_HOST'];
    }

    /**
     * Push notification for Android
     *
     * @param string $deviceToken for device token
     * @param string $title       for title
     * @param string $msg         for msg
     *
     * @return send a push notification in Android
     */
    public function sendAndroidNotification($deviceToken, $title = null, $msg = null, $soapboxId = null, $userId = null, $admin_msg = null) {
        if (!empty($deviceToken) && $deviceToken != 'NULL') {
            $title = "Wallpaper";
            $message = array();
            if(!empty($admin_msg)){
                $message = array(
                    'message' => $msg,
                    'title' => $title,
                    'message_detail' => $admin_msg
                );
            } else if(!empty($soapboxId)){
                $message = array(
                    'message' => $msg,
                    'title' => $title,
                    'soapbox_id' => $soapboxId
                );
            } else {
                $message = array(
                    'message' => $msg,
                    'title' => $title,
                    'user_id' => $userId
                );
            }
            $url = 'https://android.googleapis.com/gcm/send';
            $fields = array(
                'registration_ids' => array($deviceToken),
                'data' => $message,
            );

            $headers = array(
                'Authorization: key=' . ANDROID_API_KEY,
                'Content-Type: application/json'
            );

            // Open connection
            $ch = curl_init();

            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

            // Execute post
            $result = curl_exec($ch);

            // Close connection
            curl_close($ch);
        }
    }

    /**
     * Push notification for iphone
     *
     * @param string $deviceToken for device token
     * @param string $title       for title
     * @param string $msg         for msg
     * @param string $soapboxId      for view soapbox details
     * @param string $isQuick     checking is quick soapbox
     *
     * @return send a push notification in iphone
     */
    public function sendIphoneNotification($deviceToken, $title = null, $msg = null, $soapboxId = null, $userId = null, $admin_msg = null) {
        $passphrase = 'Multidots-iOS-11';
        $message = $msg; 
        $title = "Wallpaper";
        $filePath = '../../WallpaperProd.pem';
        if (is_file($filePath)) {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $filePath);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Open a connection to the APNS server
            $fp = "";
            if ($_SERVER['SERVER_NAME'] == "wallpaper.in") {
                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            } else {
                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            }

            if (!$fp) {
                //exit(FAILED_TO_CONNECT . $err . $errstr . PHP_EOL);
                echo FAILED_TO_CONNECT . $err . $errstr . PHP_EOL;
            }

            $messageTemp = UR_SITE_TITLE_NOTIFICATION;
            if (!empty($messageTemp)) {
                //$message = $messageTemp . ' ' . $message;
            }
            $body['aps'] = array();
            if (!empty($admin_msg)) {
                $body['aps'] = array(
                    'alert' => array(
                        'title' => $title,
                        'body' => $message,
                        'body_detail' => $admin_msg,
                        'action-loc-key' => 'Wallpaper App',
                    ),
                    'badge' => 0,
                    'sound' => 'oven.caf',
                    'content-available' => 1
                );
            } else if (!empty($userId)) {
                $body['aps'] = array(
                    'alert' => array(
                        'title' => $title,
                        'body' => $message,
                        'user_id' => $userId,
                        'action-loc-key' => 'Wallpaper App',
                    ),
                    'badge' => 0,
                    'sound' => 'oven.caf',
                    'content-available' => 1
                );
            } else {
                $body['aps'] = array(
                    'alert' => array(
                        'title' => $title,
                        'body' => $message,
                        'soapbox_id' => $soapboxId,
                        'action-loc-key' => 'Wallpaper App',
                    ),
                    'badge' => 0,
                    'sound' => 'oven.caf',
                    'content-available' => 1
                );
            }
            $payload = json_encode($body);
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            $result = fwrite($fp, $msg, strlen($msg));
            if (!$result) {
                //echo 'Message not delivered' . PHP_EOL;
                //mail('nikhil.parmar@multidots.in', 'noification', print_r($result, true));
            } else {
                //echo 'Message successfully delivered' . PHP_EOL;
                //mail('nikhil.parmar@multidots.in', 'success noification', print_r($result, true));
            }
            fclose($fp);
        }
    }

    function ismobile() {
        $is_mobile = '0';

        if (isset($_SERVER['HTTP_USER_AGENT']) and preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $is_mobile = 1;
        }
        if (isset($_SERVER['HTTP_ACCEPT']) and ( strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0)) {
            $is_mobile = 1;
        }
        if (((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $is_mobile = 1;
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
            $mobile_agents = array('w3c ', 'acs-', 'alav', 'alca', 'amoi', 'andr', 'audi', 'avan', 'benq', 'bird', 'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-');

            if (in_array($mobile_ua, $mobile_agents)) {
                $is_mobile = 1;
            }
        }

        if (isset($_SERVER['ALL_HTTP'])) {
            if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
                $is_mobile = 1;
            }
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
                $is_mobile = 0;
            }
        }

        return $is_mobile;
    }
    
    /**
    * Sets the current admin session
    *
    * @author The Chief
    * @param array $adminArr Data array of the admin
    */
   public function setAdminSession($adminArr = array()) {
           if (!empty($adminArr)) {
                   if ($this->Session->read('Config.LoggedinAdmin')) {
                           $this->Session->delete('Config.LoggedinAdmin');
                   }
                   $this->Session->write('Config.LoggedinAdmin', $adminArr);
           }
   }
    
    public function uploadImage($thumbFile = null, $dir = null){
        try {
            $dir = WWW_ROOT . $dir;
//             if (!is_dir($dir)) {
//                mkdir($dir, 0777, true);
//            }
            $name = 'cover_' . md5(date("d-m-Y h:i:s"));
            $ext = $this->ImageUpload->getFileExtensionFromUploadedFileUsingMimeType($thumbFile['tmp_name']);
            if (empty($ext) || empty($name)) {
                $this->apiErrors[] = 'Extension or name issue.';
                $validationErrors = true;
            } else {
                $image = $name . "." . $ext;
                $imageUploaded = move_uploaded_file($thumbFile['tmp_name'], $dir . "/" . $image);
                if (!$imageUploaded) {
                    $this->apiErrors[] = 'Fail to upload cover photo.';
                    $validationErrors = true;
                } else {
                    return $image;
                }
            }
        } catch (Exception $exc) {
            $this->apiErrors[] = 'something went wrong to upload thumb.';
            $validationErrors = true;
        }
    }

    function deviceType(){
        if( stripos($_SERVER['HTTP_USER_AGENT'],'iPod') || stripos($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ) {
            return $device = "ios";
        } else if( stripos($_SERVER['HTTP_USER_AGENT'],'Android') ) {
            return $device = "android";
        } else {
            return $device = "other";
        }
    }
}