<?php
/**
 * Kaaterskil Library
 *
 * @package		KaaterskilTheme
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace KaaterskilTheme\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use KaaterskilTheme\Manager;

/**
 * ManagerFactory Class
 *
 * @author Blair
 */
class ManagerFactory implements FactoryInterface {
	
	/**
	 * Factory method for KaaterskilTheme Manager service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return \KaaterskilTheme\Manager
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$config = $serviceLocator->get('Configuration');
		$manager = new Manager($serviceLocator, $config['kaaterskil_theme']);
		return $manager;
	}
}
?>