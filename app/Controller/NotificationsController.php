<?php

App::uses('AppController', 'Controller');

/**
 * EmailTemplates Controller
 *
 * @property EmailTemplate $EmailTemplate
 */
class NotificationsController extends AppController {
    /*
     * Models
     * @var array
     */

    public $uses = array('Template');
    
	/**
     * Index action
     *
     * Lists all emails
     *
     * @return void
     */
    public function index() {
        try {
            $this->paginate = array('conditions' => array('Template.type' => 1 , 'Template.status' => 1),'limit' => PAGE_LIMIT,'order' => array('subject' => 'asc'));
            $this->Template->recursive = 0;
            $this->set('emailTemplatesArr', $this->paginate('Template'));
        } catch (NotFoundException $e) {
            $this->Session->setFlash('Please check, Something is wrong.', 'flashError');
            $this->redirect("/notifications");
        }
		$this->set('title_for_layout', 'Notifications');
    }

    public function add() {
        $this->set('title_for_layout', 'Add Notification');
        if ($this->request->is('post')) {
			$isEmailTemplateExist = $this->Template->find('first',array('conditions' => array('Template.key' => $this->request->data['Template']['key'], 'Template.type' => 1, 'Template.status' => 1)));
			if(!empty($isEmailTemplateExist)){
				$this->Session->setFlash('Notification key already exist.', 'flashError');
				return false;
			}
			$this->request->data['Template']['type'] = 1;
			$this->request->data['Template']['status'] = 1;
            if ($this->Template->save($this->request->data)) {
				$this->Session->setFlash('New notification has been added successfully.', 'flashSuccess');
				$this->redirect('/notifications');
			} else {
				$this->Session->setFlash('An error occured while saving data. Please try again.', 'flashError');
			}
        }
    }

    public function edit($id = null) {
		$this->set('title_for_layout', 'Edit Notification');
        $this->Template->id = $id;
        if ($this->request->is('post') || $this->request->is('put')) {
			$isEmailTemplateExist = $this->Template->find('first', array('conditions' => array('Template.key' => $this->request->data['Template']['key'], 'Template.type' => 1, 'Template.status' => 1, 'Template.id NOT' => $id)));
			if(!empty($isEmailTemplateExist)){
				$this->Session->setFlash('Notification key already exist.', 'flashError');
				return false;
			}
            if ($this->Template->save($this->request->data)) {
                $this->Session->setFlash('Template updated.', 'flashSuccess');
                $this->redirect('/notifications');
            } else {
                $this->Session->setFlash('Something went wrong.', 'flashError');
				return false;
            }
        } else {
			$emailData = $this->Template->find('first', array('conditions' => array('Template.id' => $id, 'Template.type' => 1, 'Template.status' => 1)));
			if (!empty($emailData)) {
				$this->request->data = $emailData;
			} else {
				$this->Session->setFlash('Invalid action.', 'flashNotice');
				$this->redirect('/notifications');
			}
		}
    }

	public function view($id = null) {
		$this->set('title_for_layout', 'Notification');
        $this->Template->id = $id;
			$emailData = $this->Template->find('first', array('conditions' => array('Template.id' => $id, 'Template.type' => 1, 'Template.status' => 1)));
		if (!empty($emailData)) {
			$this->set('templateData', $emailData);
		} else {
			$this->Session->setFlash('Invalid action.', 'flashNotice');
			$this->redirect('/notifications');
		}
	}
	
	public function delete($id = null) {

        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Template->id = $id;
        $status['Template']['status'] = 0;
        if (!$this->Template->exists()) {
            throw new NotFoundException(__('Invalid Template'), 'admin_flash_error');
        }
        if ($this->Template->save($status)) {
            $this->Session->setFlash('Template Deleted.', 'flashSuccess');
            $this->redirect($this->request->referer());
        }
        $this->Session->setFlash('Template not deleted.', 'flashError');
        $this->redirect(array('action' => 'index'));
    }
}
