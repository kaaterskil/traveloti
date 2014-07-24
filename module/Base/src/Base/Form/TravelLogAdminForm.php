<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Form;

use Base\Model\User;
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * TravelLogAdminForm Class
 *
 * @author Blair
 */
class TravelLogAdminForm extends Form {
	
	private $user;
	
	public function __construct(User $user) {
		parent::__construct();
		$this->setUser($user);
		$this->init();
	}
	
	/** @return User */
	public function getUser() {
		return $this->user;
	}
	
	public function setUser(User $user) {
		$this->user = $user;
	}
	
	private function init() {
		$travelotiIdElement = new Element\Hidden();
		$travelotiIdElement->setName('traveloti_id');
		$this->add($travelotiIdElement);
		
		$connectionsElement = new Element\Select();
		$connectionsElement->setName('connections');
		$connectionsElement->setLabel('Add an Administrator');
		$connectionsElement->setValueOptions($this->getConnections());
		$this->add($connectionsElement);
		
		$submitElement = new Element\Submit();
		$submitElement->setName('submit_btn');
		$submitElement->setValue('Add');
		$submitElement->setAttribute('class', 'button');
		$submitElement->setAttribute('tabindex', -1);
		$this->add($submitElement);
	}
	
	/** @return array */
	private function getConnections() {
		/* @var $user User */
		
		$result = array();
		if($this->user != null) {
			$connections = $this->user->getAllFriends();
			foreach ($connections as $user) {
				$key = $user->getId();
				$value = $user->getDisplayName();
				$result[$key] = $value;
			}
		}
		return $result;
	}
}
?>