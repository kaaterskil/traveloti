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

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * ProfilePictureForm Class
 * @author Blair
 */
class ProfilePictureForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		$filepathElement = new Element\Hidden();
		$filepathElement->setAttributes(array(
			'name' => 'filepath',
		));
		$this->add($filepathElement);
		
		$fileElement = new Element\File();
		$fileElement->setAttributes(array(
			'id' => 'profile-upload-element',
			'name' => 'picture',
			'class' => 'profile-picture-file-upload',
		));
		$this->add($fileElement);
		
		$submit = new Element\Submit();
		$submit->setAttributes(array(
			'class' => 'profile-picture-submit-btn',
			'name' => 'submit_btn',
			'value' => 'Update',
		));
		$this->add($submit);
	}
}
?>