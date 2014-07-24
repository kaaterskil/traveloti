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
use Application\StdLib\Observer;

/**
 * Subject Class
 * @author Blair
 */
interface Observable extends Object {
	
	public function attach(Observer $observer);
	
	public function detach(Observer $observer);
	
	public function notify();
}
?>