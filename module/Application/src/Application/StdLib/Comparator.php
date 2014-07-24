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
 * Comparator Interface
 * @author Blair
 */
interface Comparator {
	public function compare(Object $o1, Object $o2);
}
?>