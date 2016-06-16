<?php

App::uses('AppModel', 'Model');

/**
 * SiteMessage Model
 *
 */
class SiteMessage extends AppModel {

	/**
	 * order
	 *
	 * @var array
	 */
	public $order = array('SiteMessage.key');

	/**
	 * Model name
	 *
	 * @var string
	 */
	public $name = 'SiteMessage';

	/**
	 * actAs
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Translate' => array(
			'message'
		)
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'type_notempty',
			),
		),
		'key' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'key_notempty',
			),
		),
		'message' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Message can not be left empty.',
			)
		),
	);

	//public $locale = 'it';
}
