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
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

/**
 * StatusForm Class
 * @author Blair
 */
class StatusForm extends Form {
	const ELEM_FORMID = 'composer_id';
	const ELEM_USERID = 'user_id';
	const ELEM_MESSAGE = 'message_text';
	const ELEM_PHOTO = 'picture';
	const ELEM_TAGS = 'tags';
	const ELEM_LOCATION = 'location';
	const ELEM_AUDIENCE = 'audience';
	const ELEM_SUBMIT = 'submit_btn';

	public function __construct() {
		parent::__construct();
		$this->init();
		$this->initFilter();
	}

	private function init() {
		$formId = new Element\Hidden();
		$formId->setAttributes(array(
			'name' => self::ELEM_FORMID,
		));
		$this->add($formId);

		$userId = new Element\Hidden();
		$userId->setAttributes(array(
			'name' => self::ELEM_USERID,
		));
		$this->add($userId);

		$message = new Element\Textarea();
		$message->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'textarea-autogrow input mentions-textarea text-input',
			'cols' => 20,
			'name' => self::ELEM_MESSAGE,
			'placeholder' => "What's on your mind?",
			'rows' => 2,
			'title' => "What's on your mind?",
		));
		$this->add($message);

		$upload = new Element\File();
		$upload->setAttributes(array(
			'name' => self::ELEM_PHOTO,
		));
		$this->add($upload);

		$tags = new Element\Text();
		$tags->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text text-input',
			'name' => self::ELEM_TAGS,
			'placeholder' => 'Who are you with?',
			'spellcheck' => 'false',
			'maxlength' => 20,
		));
		$this->add($tags);

		$location = new Element\Text();
		$location->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text text-input',
			'id' => self::ELEM_LOCATION,
			'name' => self::ELEM_LOCATION,
			'placeholder' => 'Where are you?',
			'spellcheck' => 'false',
			'maxlength' => 20,
		));
		$this->add($location);

		$audience = new Element\Select();
		$audience->setAttributes(array(
			'name' => self::ELEM_AUDIENCE,
			'value' => Constants::PRIVACY_FRIENDS,
		));
		$audience->setOptions(array(
			'value_options' => array(
				Constants::PRIVACY_EVERYONE => 'Everyone',
				Constants::PRIVACY_FRIENDS_OF_FRIENDS => 'Friends of Friends',
				Constants::PRIVACY_FRIENDS => 'Friends',
				Constants::PRIVACY_SELF => 'Only Me',
			),
		));
		$this->add($audience);
		
		$submit = new Element\Submit();
		$submit->setAttributes(array(
			'class' => 'button button-confirm button-wide composer-button-post',
			'name' => self::ELEM_SUBMIT,
			'value' => 'Post',
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
			'required' => false,
			'validators' => array(
				array(
					'name' => 'Alnum',
					'options' => array(
						'allowWhiteSpace' => true,
				)),
			),
		));
		$inputFilter->add($spec, self::ELEM_MESSAGE);
		
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
						'max' => 20,
				)),
			),
		));
		$inputFilter->add($spec, self::ELEM_TAGS);
		
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
		$inputFilter->add($spec, self::ELEM_LOCATION);
		
		$this->setInputFilter($inputFilter);
	}
}
?>