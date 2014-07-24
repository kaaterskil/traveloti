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

use Base\Model\Traveloti;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\InputFilter;

/**
 * UserForm Class
 * @author Blair
 */
class UserForm extends Form {
	const ID = 'traveloti_id';
	const TYPE = 'user_type';
	const FIRST_NAME = 'first_name';
	const MIDDLE_NAME = 'middle_name';
	const LAST_NAME = 'last_name';
	const DISPLAY_NAME_ORDER = 'display_name_order';
	const EMAIL = 'email';
	const GENDER = 'gender';
	const SUBMIT = 'submit_btn';

	public function __construct($name = null) {
		parent::__construct('user');
		$this->init();
		$this->setInputFilter($this->initFilter());
	}

	protected function init() {
		// User Id
		$id = new Element\Hidden();
		$id->setName(self::ID);
		$this->add($id);

		// User type
		$userType = new Element\Hidden();
		$userType->setName(self::TYPE);
		$this->add($userType);
		
		/*
		$userType = new Element\Select();
		$userType->setAttributes(array(
			'name' => self::TYPE,
		));
		$userType->setOptions(array(
			'label' => 'Traveloti Type',
			'value_options' => array(
				Traveloti::USER_TYPE => 'Traveler',
				Traveloti::BLOGGER_TYPE => 'Writer/Blogger',
				Traveloti::AGENT_TYPE => 'Agent',
			),
		));
		$this->add($userType);
		*/

		// First name
		$firstName = new Element\Text();
		$firstName->setAttributes(array(
			'class' => 'input-text',
			'name' => self::FIRST_NAME,
			'size' => 20,
		));
		$firstName->setOptions(array(
			'label' => 'First Name',
		));
		$this->add($firstName);

		// Middle Name
		$middleName = new Element\Text();
		$middleName->setAttributes(array(
			'class' => 'input-text',
			'name' => self::MIDDLE_NAME,
			'placeholder' => 'Optional',
			'size' => 20,
		));
		$middleName->setOptions(array(
			'label' => 'Middle Name',
		));
		$this->add($middleName);

		// Last Name
		$lastName = new Element\Text();
		$lastName->setAttributes(array(
			'class' => 'input-text',
			'name' => self::LAST_NAME,
			'size' => 20,
		));
		$lastName->setOptions(array(
			'label' => 'Last Name',
		));
		$this->add($lastName);

		// Display name order
		$displayNameOrder = new Element\Radio();
		$displayNameOrder->setAttributes(array(
			'name' => self::DISPLAY_NAME_ORDER,
		));
		$displayNameOrder->setOptions(array(
			'label' => 'Name Order',
			'unchecked_value' => 'fnln',
			'use_hidden_element' => true,
			'value_options' => array(
				'fnln' => 'Surname Last',
				'lnfn' => 'Surname First',
			),
		));
		$this->add($displayNameOrder);

		// Email
		$email = new Element\Text();
		$email->setAttributes(array(
			'class' => 'input-text',
			'name' => self::EMAIL,
			'size' => 255,
		));
		$email->setOptions(array(
			'label' => 'Email',
		));
		$this->add($email);

		// Gender
		$gender = new Element\Radio();
		$gender->setAttributes(array(
			'name' => self::GENDER,
		));
		$gender->setOptions(array(
			'label' => 'Gender',
			'unchecked_value' => '0',
			'use_hidden_element' => true,
			'value_options' => array(
				'0' => 'Female',
				'1' => 'Male',
			),
		));
		$this->add($gender);

		// Submit button
		$submitButton = new Element\Submit();
		$submitButton->setAttributes(array(
			'name' => self::SUBMIT,
			'value' => 'Update',
		));
		$this->add($submitButton);
	}

	/** @return InputFilter */
	protected function initFilter() {
		$inputFilter = new InputFilter();
		$factory = new InputFilterFactory();

		// First and Middle Names
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
		$inputFilter->add($spec, self::FIRST_NAME);
		$inputFilter->add($spec, self::MIDDLE_NAME);

		// Last Name
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
						'min' => 3,
						'max' => 20,
				)),
				array(
					'name' => 'Alnum',
					'options' => array(
						'allowWhiteSpace' => true,
				)),
			),
		));
		$inputFilter->add($spec, self::LAST_NAME);

		// Email
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
						'min' => 6,
						'max' => 255,
				)),
				array('name' => 'EmailAddress'),
			),
		));
		$inputFilter->add($spec, self::EMAIL);

		return $inputFilter;
	}
}
?>