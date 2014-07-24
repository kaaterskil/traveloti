<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Config
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Config;

/**
 * Module Class
 *
 * @author Blair
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
			'invokables' => array(
			),
			
			'factories' => array(
			),
		);
	}
}
?>