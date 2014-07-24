<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * CoverPictureForm Class
 * @author Blair
 */
class CoverPictureForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		$filepathElement = new Element\Hidden();
		$filepathElement->setName('filepath');
		$this->add($filepathElement);
		
		$fileElement = new Element\File();
		$fileElement->setAttributes(array(
			'id' => 'cover-upload-element',
			'name' => 'picture',
			'class' => 'cover-picture-file-upload',
		));
		$this->add($fileElement);
		
		$submitElement = new Element\Submit();
		$submitElement->setAttributes(array(
			'id' => 'cover-submit-btn',
			'name' => 'submit_btn',
			'class' => 'cover-picture-submit-btn',
		));
		$this->add($submitElement);
	}
}
?>