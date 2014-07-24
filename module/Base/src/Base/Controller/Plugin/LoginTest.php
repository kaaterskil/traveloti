<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Controller\Plugin;

use Zend\Http\Header\SetCookie;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * LoginTest Class
 *
 * @author Blair
 */
class LoginTest extends AbstractPlugin {

	public function test() {
		// Test for login
		$auth = $this->getController()->zfcUserAuthentication();
		if((!$auth->hasIdentity()) || ($auth->getIdentity() == null)) {
			return false;
		}
			
		// Test for Openfire nickname cookie
		$username = $auth->getIdentity()->getUsername();
		$cookies = $this->getController()->getRequest()->getCookie();
		if((isset($cookies['nickname'])) && ($cookies['nickname'] != '')) {
			if(($cookies['nickname'] != $username)) {
				// Possible stolen identity
				return false;
			}
		} else {
			// Set a cookie for the User if none exists
			$header = new SetCookie();
			$header->setName('nickname');
			$header->setValue($username);
			$header->setPath('/');
			$header->setExpires(time() + (365 * 24 * 60 * 60)); // 1 Year
			$this->getController()->getResponse()->getHeaders()->addHeader($header);
		}
		return true;
	}
}
?>