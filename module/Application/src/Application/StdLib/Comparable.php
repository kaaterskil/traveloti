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

use Application\StdLib\Object;

/**
 * Comparable Interface
 * @author Blair
 */
interface Comparable {
	public function compareTo(Object $o);
}
?>