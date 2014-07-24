<?php
/**
 * Kaaterskil Library
 *
 * @package		KaaterskilTheme
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace KaaterskilTheme\Adapter;

use Zend\Session\SessionManager;

/**
 * KaaterskilTheme adapter that returns the name of the theme specified in the session
 * @author Blair
 */
class Session extends AbstractAdapter {
	
	/**
	 * @return string
	 * @see \KaaterskilTheme\Adapter\ThemeAdapter::getTheme()
	 */
	public function getTheme() {
		$session = $this->getSession();
		if(!isset($session->KaaterskilTheme)) {
			return null;
		}
		return $session->KaaterskilTheme;
	}
	
	/**
	 * @param string $theme
	 * @see \KaaterskilTheme\Adapter\Abstractadapter::setTheme()
	 */
	public function setTheme($theme) {
		$session = $this->getSession();
		$session->KaaterskilTheme = $theme;
		return true;
	}
	
	/**
	 * @return \Zend\Session\Storage\StorageInterface
	 */
	public function getSession() {
		$sessionManager = new SessionManager();
		$sessionManager->start();
		return $sessionManager->getStorage();
	}
}
?>