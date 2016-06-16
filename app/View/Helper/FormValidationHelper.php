<?php

App::uses('AppHelper', 'View/Helper');

/**
 * FormValidationHelper
 *
 * @author The Chief
 * @package MDCRM
 */
class FormValidationHelper extends AppHelper {

	/**
	 * Helpers
	 *
	 * @var array
	 */
	public $helpers = array('Form', 'Html', 'Session');

	/**
	 * Generates a javascript validation code as per plugin syntax
	 *
	 * #Example
	 *
	 * $validationRules['Project.name'] = array(
	 * 		'required' => array(
	 * 			'rule' => array('required' => 'true'),
	 * 			'message' => __('Please enter project name.')
	 * 		)
	 * );
	 *
	 * OR
	 *
	 * $validationRules = array(
	 *		'Project.name' => array(
	 *	 		'required' => array(
	 *				'rule' => array('required' => 'true'),
	 *				'message' => __('Please enter project name.')
	 *			)
	 *		)
	 * );
	 *
	 * @author The Chief
	 * @param string $form Form id
	 * @param array $validationRules
	 * @return string Javascript code
	 */
	public function generateValidationRules($form = '', $validationRules = array()) {
		$scriptOut = "";
		$scriptRulesOut = '';
		$scriptMessagesOut = '';
		if (!empty($form) && !empty($validationRules)) {
			$scriptOut .= "$(document).ready(function(){";
			$scriptOut .= "$('{$form}').validate({";
			$scriptOut .= "ignore: null,";
			$scriptOut .= "ignore: 'input[type=\"hidden\"]',";
			if (!empty($validationRules)) {
				foreach ($validationRules as $fieldName => $rules) {
					$name = $this->Form->_name(null, $fieldName);
					$scriptRulesOut .= "'{$name['name']}' : {";
					$scriptMessagesOut .= "'{$name['name']}' : {";
					foreach ($rules as $rule) {
						$scriptRulesOut .= "'" . key($rule['rule']) . "' : " . "" . current($rule['rule']) . ",";
						$scriptMessagesOut .= "'" . key($rule['rule']) . "' : " . "'{$rule['message']}',";
					}
					$scriptRulesOut = rtrim($scriptRulesOut, ',');
					$scriptMessagesOut = rtrim($scriptMessagesOut, ',');
					$scriptRulesOut .= "},";
					$scriptMessagesOut .= "},";
				}
			}

			$scriptRulesOut = rtrim($scriptRulesOut, ',');
			$scriptMessagesOut = rtrim($scriptMessagesOut, ',');
			$scriptOut .= "rules:{{$scriptRulesOut}},";
			$scriptOut .= "messages:{{$scriptMessagesOut}}";
			$scriptOut .= "});";
			$scriptOut .= "});";
		}
		return $scriptOut;
	}

}
