<?php

App::uses('AppController', 'Controller');

/**
 * Dashboards Controller
 *
 * @property Dashboard $Dashboard
 */
class DashboardsController extends AppController {
    /*
     * Models
     * @var array
     */

    public $uses = array('Dashboard', 'User', 'UserDevice', 'Wallpaper');

    public function index() {
        $this->set('title_for_layout', 'Dashboard');
        $totalUsers = $this->User->find('count');
        $activeCount = $this->UserDevice->find('count', array('conditions' => array('UserDevice.is_login' => 1), 'group' => array('UserDevice.user_id')));
        if (!empty($totalUsers)) {
            $this->set('totalUsers', $totalUsers);
        } else {
            $this->set('totalUsers', 0);
        }
        if (!empty($activeCount)) {
            $this->set('activeCount', $activeCount);
        } else {
            $this->set('activeCount', 0);
        }
        if (!empty($totalUsers) && !empty($activeCount)) {
            $this->set('UnActiveCount', $totalUsers - $activeCount);
        } else {
            $this->set('UnActiveCount', 0);
        }
    }
}
