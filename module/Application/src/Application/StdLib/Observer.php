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
use Application\StdLib\Observable;

/**
 * Observer Interface
 * @author Blair
 */
interface Observer extends Object {
	public function update(Observable $observable);
}
?>