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
 * configuration file
 * @author Blair
 */
class Configuration extends AbstractAdapter {
	
	/**
	 * @return string
	 * @see \KaaterskilTheme\Adapter\ThemeAdapter::getTheme()
	 */
	public function getTheme() {
		$config = $this->getServiceLocator()->get('Configuration');
		if(!isset($config['kaaterskil_theme']['default_theme'])) {
			return null;
		}
		return $config['kaaterskil_theme']['default_theme'];
	}
}
?>