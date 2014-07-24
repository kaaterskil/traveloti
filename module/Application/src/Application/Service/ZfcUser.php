<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\Service;

use Base\Model\Agent;
use Base\Model\Blogger;
use Base\Model\Expert;
use Base\Model\Traveloti;
use Base\Model\User;
use Doctrine\ORM\Query\ResultSetMapping;
use ZfcUser\Controller\UserController;
use ZfcUser\Service\User as ZfcUserAuthService;

/**
 * User Service class to override ZfcUser_Service_User
 *
 * This class provides a parameterized factory for the creation of different
 * user classes. Prior to inserting a new user, the class provides a method
 * to assign a generated id (required by Doctrine) as well as create an entity
 * within the chat server system.
 * @author Blair
 */
class ZfcUser extends ZfcUserAuthService {
	/** @var UserController */
	private $controller;
	
	/** @return UserController */
	public function getController() {
		return $this->controller;
	}
	
	public function setController(UserController $controller) {
		$this->controller = $controller;
	}

	/**
	 * @see \ZfcUser\Service\User::register()
	 */
	public function register(array $data) {
		// Currently, our users all use the same class (User)
		// so we don't need this hook
		// $this->setUserEntityClass($data);

		$user = parent::register($data);
		return $user;
	}

	/**
	 * Performs any pre-insert tasks for a new User
	 *
	 * @param User $user
	 * @throws \RuntimeException
	 */
	public function preInsert(User $user) {
		$em = $this->getServiceManager()->get('\Doctrine\ORM\EntityManager');

		// Fetch the next insert id from the stored function
		// This is required by Doctrine for Class Table Inheritance
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('hash', 'hash');
		$q = $em->createNativeQuery('select sf_next_insert_id("traveloti") as hash', $rsm);
		$r = $q->getResult()[0];
		
		// Assign values to the user
		$user->setId($r['hash']);
		$user->setDisplayName();
		$user->setUsername();
		
		$chatService = $this->getServiceManager()->get('OpenfireUserService');
		if($chatService != null) {
			$result = $chatService->query(
				'add',
				$user->getUsername(),
				$user->getPassword(),
				$user->getDisplayName(),
				$user->getEmail()
			);
			$xml = simplexml_load_string($result);
			if($xml[0] != 'ok') {
				// Openfire exception
				// Note: Can't seem to fetch the controller from the service manager
				// So we'll throw an exception instead. Not pretty.
				throw new \RuntimeException("Application error: " . $xml[0]);
			}
		}
	}

	/**
	 * Parameterized factory method to create Users
	 *
	 * @param array $data
	 */
	public function setUserEntityClass(array $data) {
		$clazz = null;
		$userType = $data['type'];
		switch($userType) {
			case Traveloti::AGENT_TYPE:
				$clazz = 'Base\Model\Agent';
				break;
			case Traveloti::BLOGGER_TYPE:
				$clazz = 'Base\Model\Blogger';
				break;
			case Traveloti::USER_TYPE:
			default:
				$clazz = $this->getOptions()->getUserEntityClass();
				break;
		}
		$this->getOptions()->setUserEntityClass($clazz);
	}
}
?>