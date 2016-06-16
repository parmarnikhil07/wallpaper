<?php

/**
 * Admins controller
 *
 */
App::uses('AppController', 'Controller');

/**
 * Admins controller
 *
 */
class AdminsController extends AppController {

    /**
     * Controller name
     *
     * @var string
     */
    public $name = 'Admins';

    /**
     * Default models
     *
     * @var array
     */
    public $uses = array('Admin');
    
    /**
     * cookie component
     *
     * @var array
     */
    public $components = array('Cookie');

    /**
     * Login method
     *
     * @author The Chief
     * @return void
     */
    public function login() {
        if ($this->request->is('post')) {
            $isValidAdmin = $this->Admin->find('first', array('conditions' => array('Admin.email' => $this->request->data['Login']['email'], 'Admin.password' => md5($this->request->data['Login']['password']), 'Admin.is_deleted' => 0)));
            if (!empty($isValidAdmin)) {
                $this->setAdminSession($isValidAdmin['Admin']);
                $this->Admin->id = $isValidAdmin['Admin']['id'];
                $this->Admin->saveField('last_login', date('Y-m-d H:i:s'));
                if (!empty($this->request->data['Login']['keep_loggedin'])) {
                    Configure::write('Session.cookie', 'admin');
                    $this->RememberMe->rememberAdmin($this->request->data['Login']['email']);
                }
                $this->Session->setFlash("Successfully logged in!", 'flashSuccess');
                $this->redirect('/');
            } else {
                $this->Session->setFlash("Email address or password is wrong.", 'flashError');
            }
        }
        $this->layout = 'login';
        $this->set('title_for_layout', 'Login');
    }
    /**
     * Logout method
     *
     * @author The Chief
     * @return void
     */
    public function logout() {
        $this->Session->delete('Config.LoggedinAdmin');
        $this->RememberMe->removeRememberedUser();
        $this->Session->setFlash('Successfully logout.', 'flashSuccess');
        $this->redirect('/admins/login');
    }

}
