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
 * Country Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Model
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class Country implements InputFilterAwareInterface {
	
	private $id;
	private $isoCode;
	private $name;
	protected $inputFilter;
	
	/*----- Getter/Setters -----*/
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getIsoCode() {
		return $this->isoCode;
	}
	
	public function setIsoCode($isoCode) {
		$this->isoCode = $isoCode;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getInputFilter() {
		if(!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'country_id',
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'iso_code',
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
							'max' => 2,
						),
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'name',
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
							'max' => 100,
						),
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
		$this->id		= (isset($data['country_id']) ? $data['country_id'] : null);
		$this->isoCode	= (isset($data['iso_code']) ? $data['ido_code'] : null);
		$this->name		= (isset($data['name']) ? $data['name'] : null);
	}
	
	public function getArrayCopy() {
		$data = array(
			'country_id' => $this->getId(),
			'iso_code' => $this->getIsoCode(),
			'name' => $this->getName(),
		);
		return $data;
	}
}
?>