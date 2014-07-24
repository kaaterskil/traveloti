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
 * ImageForm Class
 *
 * @author Blair
 */
class ImageForm extends Form {

	public function __construct(){
		parent::__construct();
		$this->init();
	}

	private function init() {
		$this->setName('image_form');

		$userIdElement = new Element\Hidden();
		$userIdElement->setName('user_id');
		$this->add($userIdElement);

		$albumIdElement = new Element\Hidden();
		$albumIdElement->setName('album_id');
		$this->add($albumIdElement);

		$srcElement = new Element\Hidden();
		$srcElement->setName('src');
		$this->add($srcElement);

		$filepathElement = new Element\Hidden();
		$filepathElement->setName('filepath');
		$this->add($filepathElement);

		$captionTextElement = new Element\Textarea();
		$captionTextElement->setName('caption_text');
		$captionTextElement->setAttributes(array(
			'class' => 'textarea-no-resize textarea-autogrow caption-textarea mentions-textarea text-input',
			'placeholder' => 'Say something about this photo...',
			'title' => 'Say something about this photo...',
		));
		$this->add($captionTextElement);

		$visibilityElement = new Element\Text();
		$visibilityElement->setName('visibility');
		$this->add($visibilityElement);

		$submitButtonElement = new Element\Submit();
		$submitButtonElement->setName('btnSubmit');
		$submitButtonElement->setValue('Post Photo');
		$submitButtonElement->setAttributes(array(
		));
		$this->add($submitButtonElement);
	}
}
?>