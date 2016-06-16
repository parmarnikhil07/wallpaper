<?php

/*
 * Common Component
 *
 */
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

/**
 * Util component
 *
 * @author Rajni
 */
class UtilComponent extends Component {

    /**
     * Other components used by this component
     *
     * @var array
     */
    public $components = array();

    /**
     * Models
     *
     * @var array
     */
    public $uses = array('Contact');

    /**
     * Stores current controller object
     *
     * @var Controller
     */
    public $controller;

    /**
     * initialize
     *
     * @param string $controller controller to use;
     *
     * @return string controller
     */
    public function initialize(Controller $controller) {
        parent::initialize($controller);
        $this->controller = $controller;
    }

    /**
     * Get users Friends Id Array
     *
     * @param string $userId user_id;
     *
     * @return string userId Array
     */
    public function getUserFriendsId($userId = null) {
        $model = ClassRegistry::init('Contact');
        $friendsOne = $model->find('list', array(
            'conditions' => array('user_id' => $userId),
            'fields' => array('id', 'contact_id'),
            'group' => array('contact_id')));
        $friendsSecond = $model->find('list', array(
            'conditions' => array('contact_id' => $userId),
            'fields' => array('id', 'user_id'),
            'group' => array('user_id')));
        if (!empty($friendsOne) && !empty($friendsSecond)) {
            return array_unique(array_merge($friendsOne, $friendsSecond));
        } elseif (!empty($friendsOne) && empty($friendsSecond)) {
            return array_unique($friendsOne);
        } elseif (empty($friendsOne) && !empty($friendsSecond)) {
            return array_unique($friendsSecond);
        }
        return array();
    }

}
