<?php

/**
 * Represents an HTTP 404 error for specific case where access key is missing.
 *
 * @package       Cake.Error
 */
class MissingAccessKeyException extends HttpException {

	/**
	 * Constructor
	 *
	 * @param string $message If no message is given 'Not Found' will be the message
	 * @param int $code Status code, defaults to 404
	 */
	public function __construct($message = null, $code = 404) {
		if (empty($message)) {
			$message = 'Not Found';
		}
		parent::__construct($message, 200);
	}

}
