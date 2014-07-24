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

use Application\StdLib\Object;
use Base\Model\User;

/**
 * Minimum signature for all requests
 * @author Blair
 */
interface Request extends Object {
	/** @return int */
	public function getId();

	/** @return User */
	public function getSender();

	/** @return User */
	public function getRecipient();

	/** @return string */
	public function getMessage();
	
	/** @return boolean */
	public function getIsUnread();

	/** @return \DateTime */
	public function getCreationDate();
	
	/** @return string */
	public function getType();
}
?>