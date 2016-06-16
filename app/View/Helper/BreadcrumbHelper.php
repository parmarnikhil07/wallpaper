<?php

/**
 * Breadcrumb helper file
 */
App::uses('AppHelper', 'View/Helper');

/**
 * Breadcrumb Helper class
 *
 * @author The Chief
 */
class BreadcrumbHelper extends AppHelper {

	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Form', 'Html', 'Session');

	/**
	 * Stores all menus for breadcrumb
	 *
	 * @var array
	 */
	protected $_defaultMenu = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->setDefaultMenu();
	}

	public function setDefaultMenu() {
		$this->_defaultMenu = array(
			'menuTitle' => 'Dashboard',
			'menuTooltip' => 'Go to dashboard',
			'menuLink' => '/'
		);
	}

	public function draw() {
		$breadcrumbChildren = '';
		$breadcrumbChildren .= $this->Html->tag('li', '',array('class' => 'fa fa-globe'));
		//$breadcrumbChildren .= $this->Html->tag('li', $this->Html->link('<span class="icon16 icomoon-icon-screen-2"></span>', $this->_defaultMenu['menuLink'], array('title' => $this->_defaultMenu['menuTooltip'], 'escape' => false)), array('escape' => false));
		//$breadcrumbChildren .= '<span class="divider"><span class="icon16 icomoon-icon-arrow-right-3"></span></span>';
		$menus = $this->Session->read('Config.breadcrumbMenus');
		if (!empty($menus)) {
			foreach ($menus as $menu) {
				if (!empty($menu['isActive']) && true === $menu['isActive']) {
					$breadcrumbChildren .= $this->Html->tag('li', $menu['menuTitle'], array('escape' => false, 'class' => 'active'));
				} else {
					$breadcrumbChildren .= $this->Html->tag('li', $this->Html->link($menu['menuTitle'], $menu['menuLink'], array('title' => $menu['menuTooltip'])), array('escape' => false));
					//$breadcrumbChildren .= '<span class="divider"><span class="icon16 icomoon-icon-arrow-right-3"></span></span>';
				}
			}
		} else {
			$breadcrumbChildren .= $this->Html->tag('li', $this->_defaultMenu['menuTitle'], array('class' => 'active'));
		}
		$breadcrumbTree = $this->Html->tag('ul', $breadcrumbChildren, array('escape' => false, 'class' => 'breadcrumb'));
		echo $breadcrumbTree;
	}
}
