<?php

/*
 * Breadcrumb Component
 *
 */
App::uses('Component', 'Controller');

/**
 * Breadcrumb component class
 *
 * @author The Chief
 */
class BreadcrumbComponent extends Component {

	/**
	 * Other components used by this component
	 *
	 * @var array
	 */
	public $components = array('Session');

	/**
	 * Stores current controller object
	 *
	 * @var Controller
	 */
	public $controller;

	/**
	 * Stores breadcrumb menus
	 *
	 * @var array
	 */
	protected $_menus = array();

	public function initialize(Controller $controller) {
		parent::initialize($controller);
		$this->controller = $controller;
		$this->Session->delete('Config.breadcrumbMenus');
	}

	/**
	 * Adds a new menu to breadcrumb tree
	 *
	 * #Possible keys of $menuOptions array
	 * 		- menuTitle: Title of the menu
	 * 		- menuTooltip: Tooltip text
	 * 		- menuLink: Link to menu
	 * 		- isActive: true or false, default false if not set
	 *
	 * @author The Chief
	 * @param array $menuOptions
	 */
	public function addMenu($menuOptions = array()) {
		if (!empty($menuOptions) && is_array($menuOptions)) {
			$this->_menus[] = $menuOptions;
		}
	}

	public function beforeRender(Controller $controller) {
		parent::beforeRender($controller);
		if (!empty($this->_menus)) {
			$this->Session->write('Config.breadcrumbMenus', $this->_menus);
		}
	}

}
