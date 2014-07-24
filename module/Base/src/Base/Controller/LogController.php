<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Controller;

use Base\Form\TravelLogAdminForm;
use Base\Form\TravelLogForm;
use Base\Form\UserProfileForm;
use Base\Model\Interest;
use Base\Model\Notification;
use Base\Model\TravelLog;
use Base\Model\TravelLogAdmin;
use Base\Model\User;
use Config\Model\Profile;
use Zend\View\Model\ViewModel;

/**
 * Controls TravelLog settings
 * @author Blair
 */
class LogController extends AbstractBaseController {

	private $profileCategoryList;

	public function __construct() {
	}

	public function getProfileList() {
		if($this->profileCategoryList == null) {
			$em = $this->getEntityManager();
			$list = $em->getRepository('Config\Model\ProfileCategory')->findAll();
			$this->profileCategoryList = $list;
		}
		return $this->profileCategoryList;
	}

	public function indexAction() {
		/* @var $tl TravelLog */
		
		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		// Fetch TravelLog
		$travelLogId = $this->params('id');
		$tl = $em->getRepository('Base\Model\TravelLog')->find($travelLogId);

		$form = new TravelLogForm();
		$form->get('traveloti_id')->setValue($travelLogId);
		$form->get('about')->setValue($tl->getAbout());
		$form->get('description')->setValue($tl->getDescription());
		$form->get('general_info')->setValue($tl->getGeneralInfo());
		$form->get('location')->setValue($tl->getLocation());
		$form->get('website')->setValue($tl->getWebsite());

		$request = $this->getRequest();
		if($request->isPost()){
			$form->setData($request->getPost());
			if($form->isValid()) {
				$now = new \DateTime();
				
				// Update the values
				$tl->setAbout($form->get('about')->getValue());
				$tl->setDescription($form->get('description')->getValue());
				$tl->setGeneralInfo($form->get('general_info')->getValue());
				$tl->setLocation($form->get('location')->getValue());
				$tl->setWebsite($form->get('website')->getValue());
				$tl->setLastUpdateDate($now);
				
				// Persist the changes
				$em->flush();
				
				return $this->redirect()->toRoute('timeline', array('id' => $tl->getUsername()));
			}
		}
		
		$content = new ViewModel(array(
			'travelLogForm' => $form,
			'travelLog' => $tl,
		));
		$content->setTemplate('base/settings/log_general');

		// Construct container view model
		$headerTitle = $tl->getDisplayName();
		$vm = new ViewModel(array('headerTitle' => $headerTitle, 'traveloti' => $tl));
		$vm->setTemplate('base/settings/index');
		$vm->addChild($content, 'settingsContent');
		return $this->getView('full-page')->addChild($vm);
	}

	public function editAdminAction() {
		/* @var $admin TravelLogAdmin */
		/* @var $travelLog TravelLog */
		/* @var $user User */

		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Fetch TravelLog
		$travelLogId = $this->params('id');
		$travelLog = $em->getRepository('Base\Model\TravelLog')->find($travelLogId);
		$admins = $travelLog->getAdministrators()->getValues();

		// Test if User is an TravelLogAdmin
		$isAdmin = false;
		foreach($admins as $admin) {
			if($admin->getUser()->getId() == $userId) {
				$isAdmin = true;
				break;
			}
		}
		if(!$isAdmin) {
			return $this->redirect()->toRoute('home');
		}

		$tlf = new TravelLogAdminForm($user);
		$tlf->get('traveloti_id')->setValue($travelLog->getId());
		$content = new ViewModel(array(
			'travelLogAdminForm' => $tlf,
			'administrators' => $admins,
			'travelLog' => $travelLog,
		));
		$content->setTemplate('base/settings/log_admin');

		// Construct container view model
		$headerTitle = $travelLog->getDisplayName();
		$vm = new ViewModel(array('headerTitle' => $headerTitle, 'traveloti' => $travelLog));
		$vm->setTemplate('base/settings/index');
		$vm->addChild($content, 'settingsContent');
		return $this->getView('full-page')->addChild($vm);
	}

	public function addAdminAction() {
		/* @var $admin TravelLogAdmin */
		/* @var $travelLog TravelLog */
		/* @var $user User */
		/* @var $traveloti User */

		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		$request = $this->getRequest();
		if($request->isPost()) {
			$travelotiId = $request->getPost('connections');
			$traveloti = $em->getRepository('Base\Model\User')->find($travelotiId);

			// Fetch User
			$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
			$user = $em->getRepository('Base\Model\User')->find($userId);

			// Fetch TravelLog
			$travelLogId = $this->params('id');
			$travelLog = $em->getRepository('Base\Model\TravelLog')->find($travelLogId);


			// Test if TravelLogAdmin already exists
			$isDuplicate = false;
			$admins = $travelLog->getAdministrators()->getValues();
			foreach($admins as $admin) {
				if($admin->getUser()->getId() == $travelotiId) {
					$isDuplicate = true;
					break;
				}
			}
			if(!$isDuplicate) {
				$now = new \DateTime();

				// Create a new TravelLogAdmin
				$admin = new TravelLogAdmin();
				$admin->setCreatedBy($user);
				$admin->setCreationDate($now);
				$admin->setLastUpdateDate($now);
				$admin->setTravelLog($travelLog);
				$admin->setUser($traveloti);

				// Add the TravelLogAdmin to the TravelLog
				$travelLog->addAdministrator($admin);

				// Persist the TravelLogAdmin
				$em->persist($admin);

				// Send a notification if the TravelLogAdmin isn't the User
				if($traveloti->getId() != $user->getId()) {
					$n = $this->createNotification($user, $traveloti, $admin);
					$traveloti->addNotification($n);
					$em->persist($n);
				}

				$em->flush();
			}
		}
		return $this->redirect()->toRoute(
			'logs', array('action' => 'editAdmin', 'id' => $travelLog->getId()));
	}

	public function deleteAdminAction() {
		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		// Fetch TravelLog
		$travelLogId = $this->params('id');
		$travelLog = $em->getRepository('Base\Model\TravelLog')->find($travelLogId);

		// Fetch TravelLogAdmin
		$adminId = $this->params('oid');
		if($adminId != null) {
			$admin = $em->getRepository('Base\Model\TravelLogAdmin')->find($adminId);
			$travelLog->getAdministrators()->removeElement($admin);
			$em->remove($admin);
			$em->flush();
		}
		return $this->redirect()->toRoute(
			'logs', array('action' => 'editInterest', 'id' => $travelLog->getId()));
	}

	public function editInterestAction() {
		/* @var $travelLog TravelLog */

		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		// Fetch TravelLog
		$travelLogId = $this->params('id');
		$travelLog = $em->getRepository('Base\Model\TravelLog')->find($travelLogId);

		// Fetch the TravelLog's interests and sort them by category and name
		$interests = $travelLog->getInterests()->getValues();
		usort($interests, function($a, $b) {
			return strcmp(
				$a->getProfile()->getCategory()->getDisplayName() . $a->getProfile()->getDisplayName(),
				$b->getProfile()->getCategory()->getDisplayName() . $b->getProfile()->getDisplayName()
			);
		});

			// Construct content view model
			$pf = new UserProfileForm($this->getProfileList());
			$pf->get('user_id')->setValue($travelLogId);
			$content = new ViewModel(array(
				'profileForm' => $pf,
				'profiles' => $interests,
				'travelLog' => $travelLog,
			));
			$content->setTemplate('base/settings/log_profile');

			// Construct container view model
			$headerTitle = $travelLog->getDisplayName();
			$vm = new ViewModel(array('headerTitle' => $headerTitle, 'traveloti' => $travelLog));
			$vm->setTemplate('base/settings/index');
			$vm->addChild($content, 'settingsContent');
			return $this->getView('full-page')->addChild($vm);
	}

	public function addInterestAction() {
		/* @var $travelLog TravelLog */
		/* @var $masterProfile Profile */
		/* @var $test Interest */

		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		$request = $this->getRequest();
		if($request->isPost()) {
			$profileId = $request->getPost('profile');
			$masterProfile = $em->getRepository('Config\Model\Profile')->find($profileId);

			// Fetch TravelLog
			$travelLogId = $this->params('id');
			$travelLog = $em->getRepository('Base\Model\TravelLog')->find($travelLogId);

			// Test if interest already exists
			$isDuplicate = false;
			$interests = $travelLog->getInterests();
			foreach($interests as $test) {
				if($masterProfile->getId() == $test->getProfile()->getId()) {
					$isDuplicate = true;
					break;
				}
			}
			if(!$isDuplicate) {
				$now = new \DateTime();

				// Create a new Interest
				$interest = new Interest();
				$interest->setCreationDate($now);
				$interest->setProfile($masterProfile);
				$interest->setUser($travelLog);

				// Add the Interest to the TravelLog
				$travelLog->addInterest($interest);

				// Persist the Interest
				$em->persist($interest);
				$em->flush();
			}
		}
		return $this->redirect()->toRoute(
			'logs', array('action' => 'editInterest', 'id' => $travelLog->getId()));
	}

	public function deleteInterestAction() {
		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		// Fetch TravelLog
		$travelLogId = $this->params('id');
		$travelLog = $em->getRepository('Base\Model\TravelLog')->find($travelLogId);

		// Fetch Interest
		$interestId = $this->params('oid');
		if($interestId != null) {
			$interest = $em->getRepository('Base\Model\Interest')->find($interestId);
			$em->remove($interest);
			$em->flush();
		}
		return $this->redirect()->toRoute(
			'logs', array('action' => 'editInterest', 'id' => $travelLog->getId()));
	}
}
?>