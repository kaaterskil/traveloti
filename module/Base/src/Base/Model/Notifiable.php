<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Model;

/**
 * Notifiable interface
 * @author Blair
 */
interface Notifiable {
	
	/** @return Traveloti */
	public function getFrom();
	
	/** @return string */
	public function getType();
	
	/**
	 * Returns the text for a notification
	 *
	 * @param Traveloti $sender
	 * @return NotifierText
	 */
	public function getNotifierText(Traveloti $sender);
}
?>