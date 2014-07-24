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
 * InlineImageForm Class
 * @author Blair
 */
class InlineImageForm extends Form {
	const FILE_ELEMENT = 'picture';
	
	public function __construct(){
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		$fileElement = new Element\File();
		$fileElement->setAttributes(array(
			'name' => self::FILE_ELEMENT,
			'accept' => 'jpg,jpeg,gif,png',
		));
		$fileElement->setOptions(array(
			'label' => 'Add New Photo',
			'label_attributes' => array(
				'class' => 'photo-upload-link_title',
			),
		));
		$this->add($fileElement);
		
		$submitElement = new Element\Submit();
		$submitElement->setAttributes(array(
			'name' => 'submit_btn'
		));
		$this->add($submitElement);
	}
}
?>