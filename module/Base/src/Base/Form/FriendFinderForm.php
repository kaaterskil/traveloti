<?php
/**
 * Traveloti Library
 *
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Form;

use Config\Model\Profile;
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * FriendFinderForm Class
 *
 * @author Blair
 */
class FriendFinderForm extends Form {
	
	private $interests = array();
	
	public function __construct(array $interests = null){
		parent::__construct();
		if($interests != null) {
			$this->interests = $interests;
			$this->init();
		}
	}
	
	/** @return array */
	public function getInterests() {
		return $this->interests;
	}
	
	public function setInterests(array $interests) {
		$this->interests = $interests;
		$this->reset();
		$this->init();
	}
	
	private function init() {
		$categories = array_keys($this->interests);
		foreach ($categories as $category) {
			$interests = $this->interests[$category];
			$options = array();
			foreach($interests as $interest) {
				$key = $interest->getId();
				$value = $interest->getDisplayName();
				$options[$key] = $value;
			}
			
			$categoryElement = new Element\Select();
			$categoryElement->setAttributes(array(
				'class' => 'browser-form-element',
				'id' => substr(md5($category), 0, 8),
				'name' => $category,
			));
			$categoryElement->setLabel($category);
			$categoryElement->setLabelAttributes(array(
				'class' => 'browser-form-label',
			));
			$categoryElement->setEmptyOption('Select...');
			$categoryElement->setValueOptions($options);
			$this->add($categoryElement);
		}
	}
	
	private function reset() {
		$elements = $this->getElements();
		foreach ($elements as $element) {
			$this->remove($element);
		}
	}
}
?>