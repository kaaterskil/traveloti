<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\StdLib;

/**
 * Defines the basic methods of an Entity Object
 * @author Blair
 */
interface Object {
	/** @return bool */
	public function equals(Object $o);
	
	/** @return string */
	public function getClass();
	
	/** @return string */
	public function __toString();
}
?>