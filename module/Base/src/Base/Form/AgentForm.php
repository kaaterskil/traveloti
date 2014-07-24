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

use Application\Validator\Alnum as TravelotiAlnum;
use Base\Form\BloggerForm;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

/**
 * AgentForm Class
 * @author Blair
 */
class AgentForm extends BloggerForm {
	
	const CERTIFICATION = 'certification';
	const SPECIALIZATION = 'specialization';
	const TELEPHONE = 'telephone';
	const HOURS = 'hours';
	
	protected function init() {
		parent::init();
		
		$this->get(self::EXPERT_NAME)->setAttribute('title', 'Agency Name');
		
		$certification = new Element\Text();
		$certification->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 100,
			'name' => self::CERTIFICATION,
			'title' => 'Certifications',
		));
		$certification->setOptions(array(
			'label' => 'Certifications',
		));
		$this->add($certification);
		
		$specialization = new Element\Textarea();
		$specialization->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'textarea-autogrow text-input',
			'cols' => 20,
			'name' => self::SPECIALIZATION,
			'rows' => 3,
			'title' => 'Specializations'
		));
		$specialization->setOptions(array(
			'label' => 'Specializations',
		));
		$this->add($specialization);
		
		$telephone = new Element\Text();
		$telephone->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 15,
			'name' => self::TELEPHONE,
			'title' => 'Telephone',
		));
		$telephone->setOptions(array(
			'label' => 'Telephone',
		));
		$this->add($telephone);
		
		$hours = new Element\Text();
		$hours->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 100,
			'name' => self::HOURS,
			'title' => 'Business Hours',
		));
		$hours->setOptions(array(
			'label' => 'Business Hours',
		));
		$this->add($hours);
	}
	
	protected function initFilter() {
		$inputFilter = parent::initFilter();
		$factory = new InputFilterFactory();
		
		$alnum = new TravelotiAlnum(array(
			'allowWhiteSpace' => true,
			'allowNull' => true,
		));
		
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
		$inputFilter->add($spec, self::CERTIFICATION);
		$inputFilter->add($spec, self::HOURS);
		
		// Postal Code
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
						'max' => 10,
				)),
				array(
					'name' => 'Regex',
					'options' => array(
						'pattern' => '/^[0-9-().]+$/',
				)),
			),
		));
		$inputFilter->add($spec, self::TELEPHONE);
		
		// Postal Code
		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => false,
		));
		$inputFilter->add($spec, self::SPECIALIZATION);
		
		return $inputFilter;
	}
}
?>