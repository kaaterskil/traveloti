<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Controller;

use Base\Model\Interest;

use Zend\View\Model\ViewModel;
use Base\Form\UserProfileForm;

/**
 * Controls user profile settings
 * @author Blair
 */
class ProfileController extends AbstractBaseController {

	private $profileCategoryList;

	public function __construct() {
	}

	/*----- Getter/Setters -----*/

	public function getProfileList() {
		if(null == $this->profileCategoryList) {
			$em = $this->getEntityManager();
			$this->profileCategoryList = $em->getRepository('Config\Model\ProfileCategory')->findAll();
		}
		return $this->profileCategoryList;
	}

	/*----- Methods -----*/

	public function indexAction() {
		// Test for login
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}
		
		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $this->getEntityManager()->getRepository('Base\Model\User')->find($userId);

		// Fetch the user's profiles and sort them by category and name
		$profiles = $user->getInterests()->getValues();
		usort($profiles, function($a, $b){
			return strcmp(
				$a->getProfile()->getCategory()->getDisplayName() . $a->getProfile()->getDisplayName(),
				$b->getProfile()->getCategory()->getDisplayName() . $b->getProfile()->getDisplayName()
			);
		});

		// Construct content view model
		$pf = new UserProfileForm($this->getProfileList());
		$pf->get('user_id')->setValue($userId);
		$content = new ViewModel(array(
			'profileForm' => $pf,
			'profiles' => $profiles,
		));
		$content->setTemplate('base/settings/profile');

		// Fetch pagelets
		$welcomeBox = $this->getWelcomeBoxViewModel();
		$navigation = $this->getSidebarNavViewModel('profile', true);

		// Construct container view model
		$view = new ViewModel(array('headerTitle' => 'Main Profile'));
		$view->setTemplate('base/settings/index');
		$view->addChild($welcomeBox, 'pageletWelcomeBox');
		$view->addChild($navigation, 'pageletNavigation');
		$view->addChild($content, 'settingsContent');

		return $this->getView('left-column')->addChild($view);
	}

	public function addProfileAction() {
		$em = $this->getEntityManager();

		$request = $this->getRequest();
		if($request->isPost()){
			$profileId = $request->getPost('profile');
			$masterProfile = $em->getRepository('Config\Model\Profile')->find($profileId);

			// Fetch user
			$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
			$user = $em->getRepository('Base\Model\User')->find($userId);
				
			// Test if profile already exists
			$isDuplicate = false;
			$profiles = $user->getInterests();
			foreach($profiles as $test) {
				if($masterProfile->getId() == $test->getProfile()->getId()) {
					$isDuplicate = true;
					break;
				}
			}
			if(!$isDuplicate) {
				$now = new \DateTime();

				// Create new profile and set values
				$profile = new Interest();
				$profile->setCreationDate($now);
				$profile->setProfile($masterProfile);
				$profile->setUser($user);

				// Add profile to user
				$user->addInterest($profile);

				// Persist profile
				$em->persist($profile);
				$em->flush();
			}
		}

		return $this->redirect()->toRoute('profile');
	}

	public function deleteProfileAction() {
		$profileId = $this->params('id');
		
		$em = $this->getEntityManager();
		$profile = $em->getRepository('Base\Model\Interest')->find($profileId);

		$em->remove($profile);
		$em->flush();

		return $this->redirect()->toRoute('profile');
	}
}
?>