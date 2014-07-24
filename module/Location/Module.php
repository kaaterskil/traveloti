<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Location;

use Location\Model\Location;
use Location\Model\LocationTable;
use Location\Model\Country;
use Location\Model\CountryTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * Module Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class Module {
	
	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}
	
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	
	public function getServiceConfig() {
		return array(
			'factories' => array(
				'Location\Model\LocationTable' => function($sm) {
					$tableGateway = $sm->get('LocationTableGateway');
					$table = new LocationTable($tableGateway);
					return $table;
				},
				'LocationTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Location());
					return new TableGateway(
							'location', $dbAdapter, null, $resultSetPrototype);
				},
				'Location\Model\CountryTable' => function($sm) {
					$tableGateway = $sm->get('CountryTableGateway');
					$table = new CountryTable($tableGateway);
					return $table;
				},
				'CountryTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Country());
					return new TableGateway(
							'countries', $dbAdapter, null, $resultSetPrototype);
				},
			),
		);
	}
}
?>