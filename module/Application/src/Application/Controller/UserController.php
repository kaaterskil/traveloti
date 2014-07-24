<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\Controller;

use Zend\Http\Header\SetCookie;
use ZfcUser\Controller\UserController as ZfcUserController;

/**
 * UserController Class
 *
 * @author Blair
 */
class UserController extends ZfcUserController {
	
	public function getUserService() {
		$userService = parent::getUserService();
		if(method_exists($userService, 'setController')) {
			$userService->setController($this);
		}
		return $userService;
	}
	
	public function authenticateAction() {
		$result = parent::authenticateAction();

		$auth = $this->zfcUserAuthentication();
		if($auth->hasIdentity()) {
			$username = $auth->getIdentity()->getUsername();
			
			$cookies = $this->getRequest()->getCookie();
			// Delete the User cookie if exists
			if(isset($cookies['nickname'])) {
				unset($cookies['nickname']);
			}
			
			// Set a new cookie for the User
			$header = new SetCookie();
			$header->setName('nickname');
			$header->setValue($username);
			$header->setPath('/');
			$header->setExpires(time() + (365 * 24 * 60 * 60)); // 1 Year
			$this->getResponse()->getHeaders()->addHeader($header);
		}
		
		return $result;
	}
}
?>