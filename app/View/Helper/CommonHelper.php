<?php

App::uses('AppHelper', 'View/Helper');

/**
 * CommonHelper
 *
 * @author The Chief
 */
class CommonHelper extends AppHelper {

	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Form', 'Html', 'Session');

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->metaTagsForRobots();
		$this->__serGlobalJsVars();
	}

	public $currenciesArr = array('GBP' => 'GBP', 'USD' => 'USD', 'EUR' => 'EUR');
	
	public $currenciesSymboleArr = array('GBP' => '&#163;', 'USD' => '&#36;', 'EUR' => '&#128;');
       
	public $defaultCurrency = 'USD';
	
	public function getCurrencySymbol($currency = 'USD'){
		if(empty($currency))
		{
			$currency = 'USD';
		}
		return $this->currenciesSymboleArr[$currency];
		
	}

	/**
	 * Sets a meta tag to enable/disable robots for crawling the site
	 *
	 * @author The Chief
	 */
	public function metaTagsForRobots() {
		echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex, nofollow'), null, array('inline' => false));
	}

	/**
	 * Sets global javascript variables
	 *
	 * @author The Chief
	 */
	private function __serGlobalJsVars() {
		$script = "var SITE_URL = '" . SITE_URL . "';";
		echo $this->Html->scriptBlock($script, array('inline' => false));
	}

	public function printColorLabel($color = null, $text = 'Color') {
		if (!empty($color)) {
			echo $this->Html->tag('span', $text, array('class' => 'label', 'style' => "background-color:{$color}"));
		} else {
			echo $this->Html->tag('span', $text);
		}
	}

	public function getAvatar($imageFile = null,$options = array()) {
		$defaults = array('alt' => 'Avatar');
		$imgOptions = array_merge($defaults, $options);
		if (!empty($imageFile)) {
                    $userImage = $this->Html->image(CONFIG_AVATAR_URL . $imageFile, $imgOptions);
		}
		return $userImage;
	}
        
        public function getCompanyLogo($imageFile = null,$options = array()) {
		$defaults = array('alt' => 'Company Logo');
		$imgOptions = array_merge($defaults, $options);
		if (!empty($imageFile)) {
                    $userImage = $this->Html->image(CONFIG_COMPANY_LOGO_URL . $imageFile, $imgOptions);
		}
		return $userImage;
	}
        
        public function getProductImage($imageFile = null,$options = array()) {
		$defaults = array('alt' => 'Product Image');
		$imgOptions = array_merge($defaults, $options);
		if (!empty($imageFile)) {
                    $userImage = $this->Html->image(CONFIG_PRODUCT_IMAGE_URL . $imageFile, $imgOptions);
		}
		return $userImage;
	}
        public function getTriggerImage($imageFile = null,$options = array()) {
		$defaults = array('alt' => 'Product Image');
		$imgOptions = array_merge($defaults, $options);
		if (!empty($imageFile)) {
                    $userImage = $this->Html->image(CONFIG_TRIGGER_IMAGE_URL . $imageFile, $imgOptions);
		}
		return $userImage;
	}

}
