<?php
/**
 * Kaaterskil Library
 *
 * @package		KaaterskilTheme
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace KaaterskilTheme\Adapter;

/**
 * KaaterskilTheme adapter that returns the name of the theme specified in the
 * configuration file based on the matched route
 * @author Blair
 */
class Route extends AbstractAdapter {
	
	/**
	 * @return string
	 * @see \KaaterskilTheme\Adapter\ThemeAdapter::getTheme()
	 */
	public function getTheme() {
		$config = $this->getServiceLocator()->get('Configuration');
		$app = $this->getServiceLocator()->get('Application');
		$request = $app->getRequest();
		$router = $this->getServiceLocator()->get('Router');
		$matchedRoute = $router->match($request)->getMatchedRouteName();
		
		if(!isset($config['kaaterskil_theme']['routes'])
				|| !is_array($config['kaaterskil_theme']['routes'])) {
			return null;
		}
		
		foreach ($config['kaaterskil_theme']['routes'] as $key => $routes) {
			if(is_array($matchedRoute, $routes)) {
				return $key;
			}
		}
		return null;
	}
}
?>