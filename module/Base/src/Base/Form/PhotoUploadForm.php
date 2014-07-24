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

use Application\StdLib\Constants;
use Base\Model\Album;
use Base\Model\Photo;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

/**
 * PhotoUploadForm Class
 * @author Blair
 */
class PhotoUploadForm extends Form {
	const ELEM_USERID = 'traveloti_id';
	const ELEM_ALBUMNAME = 'album_name';
	const ELEM_ALBUMDESCRIPTION = 'album_description';
	const ELEM_ALBUMLOCATION = 'album_location';
	const ELEM_ALBUMPRIVACY = 'album_privacy';
	const ELEM_SRC = "src";
	const ELEM_FILEPATH = 'filepath';
	const ELEM_PHOTO = 'picture';
	const ELEM_CAPTION = 'caption';
	const ELEM_LOCATION = 'location';
	const ELEM_SUBMIT = 'submit_btn';

	public function __construct() {
		parent::__construct();
		$this->initElements();
		$this->initFilter();
	}

	private function initElements() {
		$userId = new Element\Hidden();
		$userId->setAttributes(array(
			'name' => self::ELEM_USERID,
		));
		$this->add($userId);

		$albumName = new Element\Text();
		$albumName->setAttributes(array(
			'class' => 'input-text',
			'name' => self::ELEM_ALBUMNAME,
			'maxlength' => 50,
			'placeholder' => Constants::ALBUM_UNDEFINED,
		));
		$albumName->setOptions(array(
			'label' => 'Album Name',
		));
		$this->add($albumName);

		$description = new Element\Text();
		$description->setAttributes(array(
			'class' => 'input-text',
			'name' => self::ELEM_ALBUMDESCRIPTION,
			'placeholder' => 'Say something about this album...'
		));
		$description->setOptions(array(
			'label' => 'Album Description',
		));
		$this->add($description);

		$albumLocation = new Element\Text();
		$albumLocation->setAttributes(array(
			'class' => 'input-text',
			'id' => self::ELEM_ALBUMLOCATION,
			'name' => self::ELEM_ALBUMLOCATION,
			'maxlength' => 100,
			'placeholder' => 'Where were these photos taken?',
		));
		$albumLocation->setOptions(array(
			'label' => 'Album Location',
		));
		$this->add($albumLocation);

		$audience = new Element\Select();
		$audience->setAttributes(array(
			'name' => self::ELEM_ALBUMPRIVACY,
			'title' => 'Album Privacy',
			'value' => Constants::PRIVACY_FRIENDS,
		));
		$audience->setOptions(array(
			'label' => 'Privacy',
			'empty_option' => 'Album Privacy...',
			'value_options' => array(
				Constants::PRIVACY_EVERYONE => 'Public',
				Constants::PRIVACY_FRIENDS_OF_FRIENDS => 'Friends of Friends',
				Constants::PRIVACY_FRIENDS => 'Friends',
				Constants::PRIVACY_SELF => 'Only Me',
			),
		));
		$this->add($audience);

		$source = new Element\Hidden();
		$source->setAttributes(array(
			'name' => self::ELEM_SRC,
		));
		$this->add($source);

		$filepath = new Element\Hidden();
		$filepath->setAttributes(array(
			'name' => self::ELEM_FILEPATH,
		));
		$this->add($filepath);
		
		$upload = new Element\File();
		$upload->setAttributes(array(
			'class' => 'input-text',
			'name' => self::ELEM_PHOTO,
			'title' => 'Select photo',
		));
		$upload->setOptions(array(
			'label' => 'Select photo',
		));
		$this->add($upload);

		$caption = new Element\Textarea();
		$caption->setAttributes(array(
			'class' => 'input-text',
			'name' => self::ELEM_CAPTION,
			'placeholder' => 'Say something about this photo...',
			'title' => 'Say something about this photo...',
		));
		$caption->setOptions(array(
			'label' => 'Caption',
		));
		$this->add($caption);

		$location = new Element\Text();
		$location->setAttributes(array(
			'class' => 'input-text',
			'id' => self::ELEM_LOCATION,
			'name' => self::ELEM_LOCATION,
			'maxlength' => 100,
			'placeholder'=> 'Where was this taken?',
		));
		$location->setOptions(array(
			'label' => 'Location',
		));
		$this->add($location);

		$submit = new Element\Submit();
		$submit->setAttributes(array(
			'class' => 'button button-confirm',
			'name' => self::ELEM_SUBMIT,
			'value' => 'Post Photo',
		));
		$this->add($submit);
	}

	private function initFilter() {
		$inputFilter = new InputFilter();
		$factory = new Factory();

		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => true,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'min' => 5,
						'max' => 50,
				)),
				array(
					'name' => 'Alnum',
					'options' => array(
						'allowWhiteSpace' => true
				)),
			),
		));
		$inputFilter->add($spec, self::ELEM_ALBUMNAME);

		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => false,
		));
		$inputFilter->add($spec, self::ELEM_ALBUMDESCRIPTION);
		$inputFilter->add($spec, self::ELEM_CAPTION);

		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => false,
			'validators' => array(
				array(
					'name' => 'StringLength',
					'options' => array(
						'encoding' => 'UTF-8',
						'max' => 100,
				)),
			),
		));
		$inputFilter->add($spec, self::ELEM_ALBUMLOCATION);
		$inputFilter->add($spec, self::ELEM_LOCATION);

		$this->setInputFilter($inputFilter);
	}
}
?>