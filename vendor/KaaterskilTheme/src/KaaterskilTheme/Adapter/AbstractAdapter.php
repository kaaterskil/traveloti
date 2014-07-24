<?php
/**
 * Kaaterskil Library
 *
 * @package		KaaterskilTheme
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace KaaterskilTheme\Adapter;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * AbstractAdapter class
 * @author Blair
 */
abstract class AbstractAdapter implements ThemeAdapter {
	
	protected $serviceLocator;
	
	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 * @see \KaaterskilTheme\Adapter\ThemeAdapter::__construct()
	 */
	public function __construct(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator() {
		return $this->serviceLocator;
	}
	
	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * @param string $theme
	 * @return boolean
	 * @see \KaaterskilTheme\Adapter\ThemeAdapter::setTheme()
	 */
	public function setTheme($theme) {
		return false;
	}
}
?>