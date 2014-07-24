<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Tests
 * @subpackage	Location
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Location\Model;

use PHPUnit_Framework_TestCase;

/**
 * LocationTest Class
 *
 * @category	Traveloti
 * @package		Traveloti_Tests
 * @subpackage	Location
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class LocationTest extends \PHPUnit_Framework_TestCase {
	
	public function testLocationInitialState() {
		$location = new Location();
		
		$this->assertNull($location->getId(), '"id" should initially be null');
		$this->assertNull($location->getAreaCode(), '"areaCode" should initially be null');
		$this->assertNull($location->getCity(), '"city" should initially be null');
		$this->assertNull($location->getCountry(), '"country" should initially be null');
		$this->assertNull($location->getLatitude(), '"latitude" should initially be null');
		$this->assertNull($location->getLongitude(), '"longitude" should initially be null');
		$this->assertNull($location->getMetroCode(), '"metroCode" should initially be null');
		$this->assertNull($location->getPostalCode(), '"postalCode" should initially be null');
		$this->assertNull($location->getRegion(), '"region" should initially be null');
	}
	
	public function testExchangeArraySetsPropertiesCorrectly() {
		$location = new Location();
		$data = array(
			'id' => 3102,
			'area_code' => 845,
			'city' => 'Kingston',
			'country' => 'US',
			'latitude' => 41.9389,
			'longitude' => -73.9901,
			'metro_code' => 501,
			'postal_code' => '12401',
			'region' => 'NY',
		);
		
		$location->exchangeArray($data);
		
		$this->assertSame($data['id'], $location->getId(), '"id" was not set correctly');
		$this->assertSame($data['area_code'], $location->getAreaCode(), '"areaCode" was not set correctly');
		$this->assertSame($data['city'], $location->getCity(), '"city" was not set correctly');
		$this->assertSame($data['country'], $location->getCountry(), '"country" was not set correctly');
		$this->assertSame($data['latitude'], $location->getLatitude(), '"latitude" was not set correctly');
		$this->assertSame($data['longitude'], $location->getLongitude(), '"longitude" was not set correctly');
		$this->assertSame($data['metro_code'], $location->getMetroCode(), '"metroCode" was not set correctly');
		$this->assertSame($data['postal_code'], $location->getPostalCode(), '"postalCode" was not set correctly');
		$this->assertSame($data['region'], $location->getRegion(), '"region" was not set correctly');
	}
	
	public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent() {
		$location = new Location();
		$data = array(
			'id' => 3102,
			'area_code' => 845,
			'city' => 'Kingston',
			'country' => 'US',
			'latitude' => 41.9389,
			'longitude' => -73.9901,
			'metro_code' => 501,
			'postal_code' => '12401',
			'region' => 'NY',
		);
		
		$location->exchangeArray($data);
		$location->exchangeArray(array());
		
		$this->assertNull($location->getId(), '"id" should have defaulted to null');
		$this->assertNull($location->getAreaCode(), '"areaCode" should have defaulted to null');
		$this->assertNull($location->getCity(), '"city" should have defaulted to null');
		$this->assertNull($location->getCountry(), '"country" should have defaulted to null');
		$this->assertNull($location->getLatitude(), '"latitude" should have defaulted to null');
		$this->assertNull($location->getLongitude(), '"longitude" should have defaulted to null');
		$this->assertNull($location->getMetroCode(), '"metroCode" should have defaulted to null');
		$this->assertNull($location->getPostalCode(), '"postalCode" should have defaulted to null');
		$this->assertNull($location->getRegion(), '"region" should have defaulted to null');
	}
	
	protected function setUp() {
		\Zend\Mvc\Application::init(include 'config/application.config.php');
	}
}
?>