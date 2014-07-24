<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\View\Helper;

use Base\Model\Notification;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * Returns the icon class for the specified notification type
 * @author Blair
 */
class NotificationIconPicker extends AbstractHelper {
	
	public function __invoke(Notification $notification) {
		$type = $notification->getType();
		
		$clazz = 'notification';
		switch($type) {
			case 'notification':
			default:
				$clazz = 'icon-notify';
		}
		return $clazz;
	}
}
?>