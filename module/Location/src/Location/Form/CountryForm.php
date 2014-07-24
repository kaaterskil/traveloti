<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Form
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Location\Form;

use Zend\Form\Form;

/**
 * CountryForm Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Form
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class CountryForm extends Form {
	
	public function __construct($name = null) {
		// We want to ignore the name passed
		parent::__construct('country');
		
		$this->setAttribute('method', 'post');
		
		$this->add(array(
			'name' => 'id',
			'attributes' => array(
				'type' => 'hidden',
			),
		));
		$this->add(array(
			'name' => 'country',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'ISO Code',
			),
		));
		$this->add(array(
			'name' => 'name',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Country Name',
			),
		));
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'save',
				'id' => 'submitbutton',
			),
		));
	}
}
?>