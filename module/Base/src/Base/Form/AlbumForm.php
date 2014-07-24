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
 * AlbumForm Class
 *
 * @author Blair
 */
class AlbumForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		$userIdElement = new Element\Hidden();
		$userIdElement->setName('user_id');
		$this->add($userIdElement);
		
		$nameElement = new Element\Text();
		$nameElement->setName('name');
		$nameElement->setOptions(array(
			'class' => 'inputtext title-input photo-album-title-input',
		));
		$this->add($nameElement);
		
		$descriptionElement = new Element\Text();
		$descriptionElement->setName('description');
		$descriptionElement->setAttributes(array(
			'class' => 'inputtext photo-album-desc-input mentions-textarea text-input',
			'placeholder' => 'Say something about this album...',
		));
		$this->add($descriptionElement);
		
		$locationElement = new Element\Text();
		$locationElement->setName('location');
		$locationElement->setOptions(array(
			'label' => '',
			'label_attributes' => array(
			),
		));
		$locationElement->setAttributes(array(
			'placeholder' => 'Where was this photo taken?',
		));
		$this->add($locationElement);
		
		$visibilityElement = new Element\Select();
		$visibilityElement->setName('visibility');
		$visibilityElement->setAttributes(array(
		));
		$this->add($visibilityElement);
		
		$submitBtn = new Element\Submit();
		$submitBtn->setName('btnSubmit');
		$this->add($submitBtn);
	}
}
?>