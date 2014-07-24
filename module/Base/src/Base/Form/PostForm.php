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

use Application\StdLib\Constants;
use Base\Model\Post;
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * LogForm Class
 *
 * @author Blair
 */
class PostForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		// Set Id element
		$idElement = new Element\Hidden('id');
		$this->add($idElement);
		
		// Set UserId element
		$userIdElement = new Element\Hidden('from_id');
		$this->add($userIdElement);
		
		// Set Message element
		$messageElement = new Element\Textarea();
		$messageElement->setName('message');
		$messageElement->setAttributes(array(
			'placeholder' => "What's on your mind?",
			'class' => 'input mentions-textarea text-input',
		));
		$this->add($messageElement);
		
		// Set Message element
		$locationElement = new Element\Textarea();
		$locationElement->setName('location');
		$locationElement->setAttributes(array(
			'class' => 'input mentions-textarea text-input',
		));
		$this->add($locationElement);
		
		$privacyElement = new Element\Select();
		$privacyElement->setName("privacy");
		$privacyElement->setValueOptions(array(
			Constants::PRIVACY_EVERYONE => 'Public',
			Constants::PRIVACY_FRIENDS_OF_FRIENDS => 'Friends of Friends',
			Constants::PRIVACY_FRIENDS => 'Friends',
			Constants::PRIVACY_SELF => 'Only Me',
		));
		$privacyElement->setValue(Constants::PRIVACY_FRIENDS);
		$this->add($privacyElement);
		
		// CreationDate is set at insert
		
		// Set Submit element
		$submitElement = new Element\Submit();
		$submitElement->setName('submit');
		$submitElement->setAttributes(array(
				'value' => 'Post',
				'class' => 'button button-confirm button-wide',
		));
		$this->add($submitElement);
	}
}
?>