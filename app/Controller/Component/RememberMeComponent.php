<?php

/*
 * RememberMe Component file
 *
 */
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

/**
 * RememberMe component
 *
 * @author The Chief
 */
class RememberMeComponent extends Component {

    /**
     * Other components used by this component
     *
     * @var array
     */
    public $components = array('Cookie');

    /**
     * Stores current controller object
     *
     * @var Controller
     */
    public $controller;

    /**
     * Cipher key for encryption/decryption
     *
     * @var string
     */
    private $__cypherKey = '17485937564892755682047369192734583655920926';

    /**
     * Cookie name
     *
     * @var string
     */
    private $__cookieName = 'esfbhoghu';

    public function initialize(Controller $controller) {
        parent::initialize($controller);
        $this->controller = $controller;
    }

    public function rememberUser($identification = null) {
        if (!empty($identification)) {
            $encryptedData = Security::cipher($identification, $this->__cypherKey);
            $this->Cookie->write($this->__cookieName, $encryptedData, true, '14 Days');
        }
    }

    public function getRememberedUser() {
        $cookieData = $this->Cookie->read($this->__cookieName);
        if (!empty($cookieData)) {
            $data = Security::cipher($cookieData, $this->__cypherKey);
            return $data;
        } else {
            return false;
        }
    }

    public function removeRememberedUser() {
        $this->Cookie->delete($this->__cookieName);
    }

}
