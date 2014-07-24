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
 * ThemeAdapter Class
 * @author Blair
 */
interface ThemeAdapter {
	
	/**
	 * Constructor
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function __construct(ServiceLocatorInterface $serviceLocator);
	
	/**
	 * Returns the name of the theme
	 * @return string
	 */
	public function getTheme();
	
	/**
	 * Persists the name of the theme
	 *
	 * @param stirng $theme
	 * @return boolean
	 */
	public function setTheme($theme);
}
?>