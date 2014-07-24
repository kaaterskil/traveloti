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
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * This form combines values for both a new album and image
 * @author Blair
 */
class CombinedAlbumImageForm extends Form {
	
	public function __construct(){
		parent::__construct('image_form');
		$this->init();
	}
	
	private function init() {
		$userIdElement = new Element\Hidden();
		$userIdElement->setName('user_id');
		$this->add($userIdElement);
		
		$albumNameElement = new Element\Text();
		$albumNameElement->setName('name');
		$albumNameElement->setAttributes(array(
			'class' => 'input-text title-input photo-album-title-input',
			'maxlength' => 65,
			'placeholder' => Constants::ALBUM_UNDEFINED,
		));
		$this->add($albumNameElement);
		
		$albumDescriptionElement = new Element\Text();
		$albumDescriptionElement->setName('description_text');
		$albumDescriptionElement->setAttributes(array(
			'class' => 'input-text photo-album-desc-input mentions-textarea text-input',
			'placeholder' => 'Say something about this album...',
		));
		$this->add($albumDescriptionElement);
		
		$albumLocationElement = new Element\Text();
		$albumLocationElement->setName('album_location');
		$albumLocationElement->setAttributes(array(
			'class' => 'input-text pls text-input',
			'placeholder' => 'Where was this taken?',
		));
		$this->add($albumLocationElement);

		$albumAudienceElement = new Element\Select();
		$albumAudienceElement->setName('album_audience');
		$albumAudienceElement->setValue(Constants::PRIVACY_FRIENDS);
		$albumAudienceElement->setEmptyOption('Album Privacy');
		$albumAudienceElement->setValueOptions(array(
			Constants::PRIVACY_EVERYONE => 'Public',
			Constants::PRIVACY_FRIENDS_OF_FRIENDS => 'Friends of Friends',
			Constants::PRIVACY_FRIENDS => 'Friends',
			Constants::PRIVACY_SELF => 'Only Me',
		));
		$this->add($albumAudienceElement);

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
		
		$locationElement = new Element\Text();
		$locationElement->setName('location');
		$locationElement->setAttributes(array(
			'class' => 'input-text pls places-input text-input',
			'placeholder' => 'Where was this?',
			'onchange' => 'Traveloti.locationLister(this);'
		));
		$this->add($locationElement);

		/*
		$audienceElement = new Element\Select();
		$audienceElement->setName('audience');
		$audienceElement->setValue(Constants::PRIVACY_EVERYONE);
		$audienceElement->setValueOptions(array(
			Constants::PRIVACY_EVERYONE => 'Public',
			Constants::PRIVACY_FRIENDS_OF_FRIENDS => 'Friends of Friends',
			Constants::PRIVACY_FRIENDS => 'Friends',
			Constants::PRIVACY_SELF => 'Only Me',
		));
		$this->add($audienceElement);
		*/

		$submitButtonElement = new Element\Submit();
		$submitButtonElement->setName('post_photos_button');
		$submitButtonElement->setAttributes(array(
			'class' => 'mls done-button button button-confirm',
			'value' => 'Post Photo',
		));
		$this->add($submitButtonElement);
	}
}
?>