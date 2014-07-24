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

use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

/**
 * LocationTableTest Class
 *
 * @category	Traveloti
 * @package		Traveloti_Tests
 * @subpackage	Location
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class LocationTableTest extends PHPUnit_Framework_TestCase {
	
	public function testFetchAllReturnsAllAlbums() {
		$resultSet = new ResultSet();
		$mockTableGateway = $this->getMock(
			'Zend\Db\TableGateway\TableGateway',
			array('select'),
			array(),
			'',
			false
		);
		$mockTableGateway->expects($this->once())
						 ->method('select')
						 ->with()
						 ->will($this->returnValue($resultSet));
	
		$locationTable = new LocationTable($mockTableGateway);
	
		$this->assertSame($resultSet, $locationTable->fetchAll());
	}
	
	public function testCanRetrieveALocationByItsId() {
		$location = new Location();
		$data = array(
			'id' => 999999,
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
	
		$resultSet = new ResultSet();
		$resultSet->setArrayObjectPrototype(new Location());
		$resultSet->initialize(array($location));
	
		$mockTableGateway = $this->getMock(
			'Zend\Db\TableGateway\TableGateway',
			array('select'),
			array(),
			'',
			false
		);
		$mockTableGateway->expects($this->once())
						 ->method('select')
						 ->with(array('id' => 999999))
						 ->will($this->returnValue($resultSet));
	
		$locationTable = new LocationTable($mockTableGateway);
	
		$this->assertSame($location, $locationTable->getLocation(999999));
	}
	
	public function testCanDeleteAnAlbumByItsId() {
		$mockTableGateway = $this->getMock(
			'Zend\Db\TableGateway\TableGateway',
			array('delete'),
			array(),
			'',
			false
		);
		$mockTableGateway->expects($this->once())
						 ->method('delete')
						 ->with(array('id' => 999999));
	
		$locationTable = new LocationTable($mockTableGateway);
		$locationTable->deleteLocation(999999);
	}
	
	public function testSaveAlbumWillInsertNewAlbumsIfTheyDontAlreadyHaveAnId() {
		$location = new Location();
		$data = array(
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
	
	    $mockTableGateway = $this->getMock(
	    	'Zend\Db\TableGateway\TableGateway',
	    	array('insert'),
	    	array(),
	    	'',
	    	false
	    );
	    $mockTableGateway->expects($this->once())
	                     ->method('insert')
	                     ->with($data);

	    $locationTable = new LocationTable($mockTableGateway);
	    $locationTable->saveLocation($location);
	}
	
	
	public function testSaveAlbumWillUpdateExistingAlbumsIfTheyAlreadyHaveAnId() {
		$location = new Location();
		$data = array(
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
	
	    $resultSet = new ResultSet();
	    $resultSet->setArrayObjectPrototype(new Location());
	    $resultSet->initialize(array($location));
	
	    $mockTableGateway = $this->getMock(
    		'Zend\Db\TableGateway\TableGateway',
    		array('select', 'update'),
    		array(),
    		'',
    		false
	    );
	    $mockTableGateway->expects($this->once())
	                     ->method('select')
	                     ->with(array('id' => 999999))
	                     ->will($this->returnValue($resultSet));
	    $mockTableGateway->expects($this->once())
	                     ->method('update')
	                     ->with($data, array('id' => 999999));

	    $locationTable = new LocationTable($mockTableGateway);
	    $locationTable->saveLocation($location);
	}
	
	public function testExceptionIsThrownWhenGettingNonexistentLocation() {
	    $resultSet = new ResultSet();
	    $resultSet->setArrayObjectPrototype(new Location());
	    $resultSet->initialize(array());
	
	    $mockTableGateway = $this->getMock(
	    	'Zend\Db\TableGateway\TableGateway',
	    	array('select'),
	    	array(),
	    	'',
	    	false
	    );
	    $mockTableGateway->expects($this->once())
	                     ->method('select')
	                     ->with(array('id' => 999999))
	                     ->will($this->returnValue($resultSet));

	    $locationTable = new LocationTable($mockTableGateway);
	
	    try {
	    	$locationTable->getLocation(999999);
	    } catch (\Exception $e) {
	        $this->assertSame('Could not find row 999999', $e->getMessage());
	        return;
	    }
	
	    $this->fail('Expected exception was not thrown');
	}
	
	protected function setUp() {
        \Zend\Mvc\Application::init(include 'config/application.config.php');
	}
}
?>