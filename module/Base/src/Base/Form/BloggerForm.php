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
use Base\Form\UserForm;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

/**
 * BloggerForm Class
 *
 * @author Blair
 */
class BloggerForm extends UserForm {
	
	const SINCE = 'blogger_since';
	const EXPERT_NAME = 'expert_name';
	const ADDRESS1 = 'address1';
	const ADDRESS2 = 'address2';
	const CITY = 'city';
	const REGION = 'region';
	const POSTAL_CODE = 'postal_code';
	const BIO = 'biography';
	const TESTIMONIAL = 'testimonial';
	const LINK_WEBSITE = 'link_website';
	const LINK_FACEBOOK = 'link_facebook';
	const LINK_TWITTER = 'link_twitter';
	const LINK_LINKEDIN = 'link_linkedin';
	const LINK_YOUTUBE = 'link_youtube';
	const LINK_BLOG = 'link_blog';
	
	protected function init() {
		parent::init();
		
		$since = new Element\Text();
		$since->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 4,
			'name' => self::SINCE,
			'size' => 10,
			'title' => 'In Business Since',
		));
		$since->setOptions(array(
			'label' => 'In Business Since',
		));
		$this->add($since);
		
		$expertName = new Element\Text();
		$expertName->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 100,
			'name' => self::EXPERT_NAME,
			'title' => 'Blog/Agency Name',
		));
		$expertName->setOptions(array(
			'label' => 'Blog/Agency Name',
		));
		$this->add($expertName);
		
		$address1 = new Element\Text();
		$address1->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 100,
			'name' => self::ADDRESS1,
			'title' => 'Address',
		));
		$address1->setOptions(array(
			'label' => 'Address',
		));
		$this->add($address1);
		
		$address2 = new Element\Text();
		$address2->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 100,
			'name' => self::ADDRESS2,
		));
		$address2->setOptions(array(
			'label' => '&nbsp;',
		));
		$this->add($address2);
		
		$city = new Element\Text();
		$city->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 60,
			'name' => self::CITY,
			'title' => 'City',
		));
		$city->setOptions(array(
			'label' => 'City',
		));
		$this->add($city);
		
		$region = new Element\Text();
		$region->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 2,
			'name' => self::REGION,
			'title' => 'State/Region',
		));
		$region->setOptions(array(
			'label' => 'State/Region',
		));
		$this->add($region);
		
		$postalCode = new Element\Text();
		$postalCode->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 10,
			'name' => self::POSTAL_CODE,
			'title' => 'Postal Code',
		));
		$postalCode->setOptions(array(
			'label' => 'Postal Code',
		));
		$this->add($postalCode);
		
		$bio = new Element\Textarea();
		$bio->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'textarea-autogrow text-input',
			'cols' => 20,
			'name' => self::BIO,
			'rows' => 3,
			'title' => 'Biography',
		));
		$bio->setOptions(array(
			'label' => 'Biography',
		));
		$this->add($bio);
		
		$testimonial = new Element\Textarea();
		$testimonial->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'textarea-autogrow text-input',
			'cols' => 20,
			'name' => self::TESTIMONIAL,
			'rows' => 3,
			'title' => 'Testimonial(s)',
		));
		$testimonial->setOptions(array(
			'label' => 'Testimonial(s)',
		));
		$this->add($testimonial);
		
		$website = new Element\Text();
		$website->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => self::LINK_WEBSITE,
			'title' => 'Website',
		));
		$website->setOptions(array(
			'label' => 'Website',
		));
		$this->add($website);
		
		$facebookLink = new Element\Text();
		$facebookLink->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => self::LINK_FACEBOOK,
			'title' => 'Facebook Link',
		));
		$facebookLink->setOptions(array(
			'label' => 'Facebook Link',
		));
		$this->add($facebookLink);
		
		$twitterLink = new Element\Text();
		$twitterLink->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => self::LINK_TWITTER,
			'title' => 'Twitter Link',
		));
		$twitterLink->setOptions(array(
			'label' => 'Twitter Link',
		));
		$this->add($twitterLink);
		
		$linkedinLink = new Element\Text();
		$linkedinLink->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => self::LINK_LINKEDIN,
			'title' => 'LinkedIn Link',
		));
		$linkedinLink->setOptions(array(
			'label' => 'LinkedIn Link',
		));
		$this->add($linkedinLink);
		
		$youtubeLink = new Element\Text();
		$youtubeLink->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => self::LINK_YOUTUBE,
			'title' => 'YouTube Link',
		));
		$youtubeLink->setOptions(array(
			'label' => 'YouTube Link',
		));
		$this->add($youtubeLink);
		
		$blogLink = new Element\Text();
		$blogLink->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => self::LINK_BLOG,
			'title' => 'Blog Link',
		));
		$blogLink->setOptions(array(
			'label' => 'Blog Link',
		));
		$this->add($blogLink);
	}
	
	protected function initFilter() {
		$inputFilter = parent::initFilter();
		$factory = new InputFilterFactory();
		
		$alnumValidator = new TravelotiAlnum(array(
			'allowWhiteSpace' => true,
			'allowNull' => true,
		));
		
		// Since
		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => false,
			'validators' => array(
				array(
					'name' => 'Date',
					'options' => array(
						'format' => 'Y',
				)),
			),
		));
		$inputFilter->add($spec, self::SINCE);
		
		// General
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
		$spec->getValidatorChain()->addValidator($alnumValidator);
		$inputFilter->add($spec, self::EXPERT_NAME);
		$inputFilter->add($spec, self::ADDRESS1);
		$inputFilter->add($spec, self::ADDRESS2);
		$inputFilter->add($spec, self::CITY);
		
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
					'name' => 'PostCode',
					'options' => array(
						'locale' => 'en_us',
				)),
			),
		));
		$inputFilter->add($spec, self::POSTAL_CODE);
		
		// Biography and Testimonial
		$spec = $factory->createInput(array(
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'required' => false,
		));
		$spec->getValidatorChain()->addValidator($alnumValidator);
		$inputFilter->add($spec, self::BIO);
		$inputFilter->add($spec, self::TESTIMONIAL);
		
		// Url addresses
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
						'max' => 255,
				)),
			),
		));
		$inputFilter->add($spec, self::LINK_BLOG);
		$inputFilter->add($spec, self::LINK_FACEBOOK);
		$inputFilter->add($spec, self::LINK_LINKEDIN);
		$inputFilter->add($spec, self::LINK_TWITTER);
		$inputFilter->add($spec, self::LINK_WEBSITE);
		$inputFilter->add($spec, self::LINK_YOUTUBE);
		
		return $inputFilter;
	}
}
?>