<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * The form to add a profile to a traveloti member
 * @author Blair
 */
class UserProfileForm extends Form {
	
	private $profileList;
	
	public function __construct(array $profileList = null) {
		parent::__construct();
		$this->setProfielList($profileList);
		$this->init();
	}
	
	public function getProfileList() {
		return $this->profileList;
	}
	
	public function setProfielList(array $profileList = array()) {
		$this->profileList = $profileList;
	}
	
	private function init() {
		$userIdElement = new Element\Hidden();
		$userIdElement->setName('user_id');
		$this->add($userIdElement);
		
		$profileElement = new Element\Select();
		$profileElement->setName('profile');
		$profileElement->setLabel('Add a Profile');
		$profileElement->setValueOptions($this->getProfileOptions());
		$this->add($profileElement);

		$submitElement = new Element\Submit();
		$submitElement->setName('btnSubmit');
		$submitElement->setValue('Add');
		$submitElement->setAttribute('class', 'button');
		$submitElement->setAttribute('tabindex', -1);
		$this->add($submitElement);
	}
	
	private function getProfileOptions() {
		$list = $this->getProfileList();
		
		$valueOptions = array();
		foreach($list as $category) {
			$label = $category->getDisplayName();
			$profiles = $category->getProfiles()->getValues();
			$options = array();
			foreach($profiles as $profile) {
				$key = $profile->getId();
				$value = $profile->getDisplayName();
				$options[$key] = $value;
			}
			$valueOptions[] = array('label' => $label, 'options' => $options);
		}
		return $valueOptions;
	}
}
?>