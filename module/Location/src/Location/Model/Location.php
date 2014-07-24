<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Model
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Location\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Location Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Model
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class Location implements InputFilterAwareInterface {
	
	private $id;
	private $areaCode;
	private $city;
	private $country;
	private $latitude;
	private $longitude;
	private $metroCode;
	private $postalCode;
	private $region;
	protected $inputFilter;
	
	/*----- Getter/Setters -----*/
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getAreaCode() {
		return $this->areaCode;
	}
	
	public function setAreaCode($areaCode) {
		$this->areaCode = $areaCode;
	}
	
	public function getCity() {
		return $this->city;
	}
	
	public function setCity($city) {
		$this->city = $city;
	}
	
	public function getCountry() {
		return $this->country;
	}
	
	public function setCountry($country) {
		$this->country = $country;
	}
	
	public function getLatitude() {
		return $this->latitude;
	}
	
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}
	
	public function getLongitude() {
		return $this->longitude;
	}
	
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}
	
	public function getMetroCode() {
		return $this->metroCode;
	}
	
	public function setMetroCode($metroCode) {
		$this->metroCode = $metroCode;
	}
	
	public function getPostalCode(){
		return $this->postalCode;
	}
	
	public function setPostalCode($postalCode) {
		$this->postalCode = $postalCode;
	}
	
	public function getRegion() {
		return $this->region;
	}
	
	public function setRegion($region) {
		$this->region = $region;
	}
	
	public function getInputFilter() {
		if(!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'location_id',
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'country_id',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 2,
							'max' => 2,
						),
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'region',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 2,
							'max' => 2,
						),
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'city',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 0,
							'max' => 50,
						),
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'postal_code',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 1,
							'max' => 5,
						),
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'metro_code',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 3,
							'max' => 3,
						),
					),
					array(
						'name' => 'Digits',
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'area_code',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min' => 3,
							'max' => 3,
						),
					),
					array(
						'name' => 'Digits',
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'latitude',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'Float',
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'longitude',
				'required' => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'Float',
					),
				),
			)));
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception('not used');
	}
	
	/*----- Methods -----*/
	
	public function exchangeArray($data) {
		$this->id			= (isset($data['locaion_id'])) ? $data['location_id'] : null;
		$this->areaCode		= (isset($data['area_code'])) ? $data['area_code'] : null;
		$this->city			= (isset($data['city'])) ? $data['city'] : null;
		$this->country		= (isset($data['country_id'])) ? $data['country_id'] : null;
		$this->latitude		= (isset($data['latitude'])) ? $data['latitude'] : null;
		$this->longitude	= (isset($data['longitude'])) ? $data['longitude'] : null;
		$this->metroCode	= (isset($data['metro_code'])) ? $data['metro_code'] : null;
		$this->postalCode	= (isset($data['postal_code'])) ? $data['postal_code'] : null;
		$this->region		= (isset($data['region'])) ? $data['region'] : null;
	}
	
	public function getArrayCopy() {
		$data = array(
			'location_id' => $this->getId(),
			'area_code' => $this->getAreaCode(),
			'city' => $this->getCity(),
			'country_id' => $this->getCountry(),
			'latitude' => $this->getLatitude(),
			'longitude' => $this->getLongitude(),
			'metro_code' => $this->getMetroCode(),
			'postal_code' => $this->getPostalCode(),
			'region' => $this->getRegion(),
		);
		return $data;
	}
}
?>