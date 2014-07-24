<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\StdLib\Exception;

/**
 * Thrown to indicate that the code has attempted to cast an object
 * to a subclass of which it is not an instance.
 * @author Blair
 */
class ClassCastException extends \RuntimeException {
	
	public function __construct($message = null, $code = null, $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
?>