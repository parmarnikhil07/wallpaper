<?php

App::uses('AppController', 'Controller');

/**
 * Authorize controller
 *
 * @package    URateIt
 * @subpackage URateIt.Controllers
 */
class AuthorizeController extends AppController {

    /**
     * Controller name
     *
     * @var string
     */
    public $name = 'Authorize';

    /**
     * Models
     *
     * @var array
     */
    public $uses = array('ApiKey');

    /**
     * API method to get an access key for mobile app
     * This method requires Public Key.
     *
     * API URL: /authorize/get_access_key/{PUBLIC_KEY}
     *
     * @param string $publicKey pass public key
     *
     * @return mixed
     */
    public function get_access_key($publicKey = null) {
        if (empty($publicKey)) {
            $this->apiResponseCode = API_VALIDATION_ERROR;
            $this->apiErrors[] = "Public key missing";
        } elseif ($publicKey != API_PUBLIC_KEY) {
            $this->apiResponseCode = API_VALIDATION_ERROR;
            $this->apiErrors[] = "Wrong public key";
        } else {
            $accessKey = $this->Common->generateApiAccessKey();
            $userAgentData = $this->Common->parseUserAgent();
            $apiKeyData['platform'] = !empty($userAgentData['platform']) ? $userAgentData['platform'] : '';
            $apiKeyData['access_key'] = $accessKey;
            $apiKeyData['ip_address'] = $this->request->clientIp();
            $apiKeyData['is_mobile'] = ($this->request->is('mobile')) ? 1 : 0;
            if ($this->ApiKey->save($apiKeyData)) {
                $this->apiResponseCode = API_CODE_SUCCESS;
                $this->apiOutputArr['access_key'] = $accessKey;
            } else {
                $this->apiResponseCode = API_SAVE_ERROR;
                 $this->apiErrors[] = "Fail to save";
            }
        }
    }
}
