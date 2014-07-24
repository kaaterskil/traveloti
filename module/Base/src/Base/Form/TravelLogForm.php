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

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * TravelLogForm Class
 * @author Blair
 */
class TravelLogForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		$idElement = new Element\Hidden();
		$idElement->setName('traveloti_id');
		$this->add($idElement);
		
		$nameElement = new Element\Text();
		$nameElement->setAttributes(array(
			'class' => 'input-text',
			'maxlength' => 50,
			'name' => 'name',
			'placeholder' => 'What do you want to call your Travel Log?',
			'title' => 'What do you want to call your Travel Log?',
		));
		$this->add($nameElement);
		
		$aboutElement = new Element\Text();
		$aboutElement->setAttributes(array(
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => 'about',
			'placeholder' => 'Enter something for an About section...',
			'title' => 'Enter something for an About section...',
		));
		$this->add($aboutElement);
		
		$descriptionElement = new Element\Text();
		$descriptionElement->setAttributes(array(
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => 'description',
			'placeholder' => 'Enter a brief description...',
			'title' => 'Where is this?',
		));
		$this->add($descriptionElement);
		
		$locationElement = new Element\Text();
		$locationElement->setAttributes(array(
			'class' => 'input-text',
			'maxlength' => 100,
			'name' => 'location',
			'placeholder' => 'Where is this?',
			'title' => 'Where is this?',
		));
		$this->add($locationElement);
		
		$pictureElement = new Element\File();
		$pictureElement->setLabel('Select a cover photo:');
		$pictureElement->setAttributes(array(
			'class' => '',
			'name' => 'cover_picture',
			'title' => 'Select a cover photo',
		));
		$this->add($pictureElement);
		
		$generalInfoElement = new Element\Textarea();
		$generalInfoElement->setAttributes(array(
			'class' => 'input-textarea-full',
			'name' => 'general_info',
			'placeholder' => 'Please provide some basic info',
			'title' => 'Please provide some basic info',
		));
		$this->add($generalInfoElement);
		
		$websiteElement = new Element\Text();
		$websiteElement->setAttributes(array(
			'class' => 'input-text',
			'maxlength' => 255,
			'name' => 'website',
			'placeholder' => 'Your website, Twitter or Yelp link',
			'title' => 'Your website, Twitter or Yelp link',
		));
		$this->add($websiteElement);
		
		$submitButton = new Element\Submit();
		$submitButton->setAttributes(array(
			'class' => 'button button-confirm button-wide',
			'name' => 'submit_btn',
			'value' => 'Create Your Travel Log',
			'title' => 'Create Your Travel Log',
		));
		$this->add($submitButton);
	}
}
?>