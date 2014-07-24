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
 * LogForm Class
 *
 * @author Blair
 */
class CommentForm extends Form {

	public function __construct() {
		parent::__construct();
		$this->init();
	}

	private function init() {
		$postIdElement = new Element\Hidden();
		$postIdElement->setAttributes(array(
			'name' => 'post_id',
		));
		$this->add($postIdElement);
		
		$postTypeElement = new Element\Hidden();
		$postTypeElement->setAttributes(array(
			'name' => 'type',
		));
		$this->add($postTypeElement);

		$link = new Element\Button();
		$link->setName('comment_link');
		$link->setValue('Comment');
		$link->setLabel('Comment');
		$link->setLabelAttributes(array(
			'class' => 'link-button comment-link',
		));
		$link->setAttributes(array(
			'onclick' => 'return fc_click(this)',
		));
		$this->add($link);

		// Set Message element
		$messageElement = new Element\Textarea();
		$messageElement->setName('message');
		$messageElement->setAttributes(array(
			'class' => 'input mentions-textarea text-input textarea-autogrow textarea-no-resize ufi-add-comment-input',
			'placeholder' => 'Write a comment...',
			'title' => 'Write a comment...',
		));
		$this->add($messageElement);

		// From, CreationDate and State fields are set at insert

		// Set Submit element
		$submitElement = new Element\Submit();
		$submitElement->setName('btnSubmit');
		$submitElement->setAttribute('tabindex', -1);
		$this->add($submitElement);
	}
}
?>