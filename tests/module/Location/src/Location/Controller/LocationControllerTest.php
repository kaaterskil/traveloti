<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Tests
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Location\Controller;

use Location\Controller\LocationController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

/**
 * LocationControllerTest Class
 *
 * @category	Traveloti
 * @package		Traveloti_Tests
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class LocationControllerTest extends PHPUnit_Framework_TestCase {

	protected $controller;
	protected $request;
	protected $response;
	protected $routeMatch;
	protected $event;
	
	public function testAddActionCanBeAccessed() {
		$this->routeMatch->setParam('action', 'add');
	
		$result   = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
	
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
	}
	
	public function testDeleteActionCanBeAccessed() {
		$this->routeMatch->setParam('action', 'delete');
	
		$result   = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
	
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
	}
	
	public function testEditActionCanBeAccessed() {
		$this->routeMatch->setParam('action', 'edit');
	
		$result   = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
	
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
	}
	
	public function testIndexActionCanBeAccessed() {
		$this->routeMatch->setParam('action', 'index');
	
		$result   = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
	
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
	}
	
	public function testGetLocationTableReturnsAnInstanceOfLocationTable() {
		$this->assertInstanceOf(
			'Location\Model\LocationTable',
			$this->controller->getLocationTable()
		);
	}
	
	protected function setUp() {
		$bootstrap        = \Zend\Mvc\Application::init(include 'config/application.config.php');
		$this->controller = new LocationController();
		$this->request    = new Request();
		$this->routeMatch = new RouteMatch(array('controller' => 'index'));
		$this->event      = $bootstrap->getMvcEvent();
		$this->event->setRouteMatch($this->routeMatch);
		$this->controller->setEvent($this->event);
		$this->controller->setEventManager($bootstrap->getEventManager());
		$this->controller->setServiceLocator($bootstrap->getServiceManager());
	}
}
?>