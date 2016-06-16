<?php

App::uses('AppModel', 'Model');

/**
 * Build Model
 *
 */
class Build extends AppModel {

	/**
	 * Model name
	 *
	 * @var string
	 */
	public $name = 'Build';

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'version' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter Version.',
			),
		),
		'build_number' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter Build.',
			),
		),
		'build_url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please enter Build.',
			)
		),
	);

	//public $locale = 'it';
}
