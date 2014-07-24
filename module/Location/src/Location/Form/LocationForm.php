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
 * LocationForm Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Form
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class LocationForm extends Form {
	
	public function __construct($name = null) {
		// We want tp ignore the name passed
		parent::__construct('location');
		$this->setAttribute('method', 'post');
		$this->add(array(
			'name' => 'location_id',
			'attributes' => array(
				'type' => 'hidden',
			),
		));
		$this->add(array(
			'name' => 'country_id',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Country',
			),
		));
		$this->add(array(
			'name' => 'region',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Region',
			),
		));
		$this->add(array(
			'name' => 'city',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'City',
			),
		));
		$this->add(array(
			'name' => 'postal_code',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Postal Code',
			),
		));
		$this->add(array(
			'name' => 'metro_code',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Metro Code',
			),
		));
		$this->add(array(
			'name' => 'area_code',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Area Code',
			),
		));
		$this->add(array(
			'name' => 'latitude',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Latitude',
			),
		));
		$this->add(array(
			'name' => 'longitude',
			'attributes' => array(
				'type' => 'text',
			),
			'options' => array(
				'label' => 'Longitude',
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