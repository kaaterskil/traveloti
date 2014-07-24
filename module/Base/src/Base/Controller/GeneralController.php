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

use Base\Model\Expert;

use Base\Form\AgentForm;
use Base\Form\BloggerForm;
use Base\Form\UserForm;
use Base\Model\Traveloti;
use Base\Model\User;
use Zend\View\Model\ViewModel;

/**
 * Controls general user settings
 * @author Blair
 */
class GeneralController extends AbstractBaseController {

	public function __construct() {
	}

	/*----- Methods -----*/

	public function indexAction() {
		/* @var $user User */

		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		$form = null;
		switch ($user->getUserType()) {
			case Traveloti::AGENT_TYPE:
				$form = new AgentForm();
				break;
			case Traveloti::BLOGGER_TYPE:
				$form = new BloggerForm();
				break;
			case Traveloti::USER_TYPE:
			default:
				$form = new UserForm();
				break;
		}

		$form->get($form::ID)->setValue($userId);
		$form->get($form::DISPLAY_NAME_ORDER)->setValue($user->getDisplayNameOrder());
		$form->get($form::EMAIL)->setValue($user->getEmail());
		$form->get($form::FIRST_NAME)->setValue($user->getFirstName());
		$form->get($form::GENDER)->setValue($user->getGender());
		$form->get($form::LAST_NAME)->setValue($user->getLastName());
		$form->get($form::MIDDLE_NAME)->setValue($user->getMiddleName());
		$form->get($form::TYPE)->setValue($user->getUserType());

		if(($user->getUserType() == Traveloti::AGENT_TYPE)
			|| ($user->getUserType() == Traveloti::BLOGGER_TYPE)) {

			$form->get($form::SINCE)->setValue($user->getSince());
			$form->get($form::EXPERT_NAME)->setValue($user->getExpertName());
			$form->get($form::ADDRESS1)->setValue($user->getAddress1());
			$form->get($form::ADDRESS2)->setValue($user->getAddress2());
			$form->get($form::CITY)->setValue($user->getCity());
			$form->get($form::REGION)->setValue($user->getRegion());
			$form->get($form::POSTAL_CODE)->setValue($user->getPostalCode());
			$form->get($form::BIO)->setValue($user->getBio());
			$form->get($form::TESTIMONIAL)->setValue($user->getTestimonial());
			$form->get($form::LINK_BLOG)->setValue($user->getBlogLink());
			$form->get($form::LINK_FACEBOOK)->setValue($user->getFacebookLink());
			$form->get($form::LINK_LINKEDIN)->setValue($user->getLinkedinLink());
			$form->get($form::LINK_TWITTER)->setValue($user->getTwitterLink());
			$form->get($form::LINK_WEBSITE)->setValue($user->getWebsite());
			$form->get($form::LINK_YOUTUBE)->setValue($user->getYoutubeLink());
		}
		if($user->getUserType() == Traveloti::AGENT_TYPE) {
			$form->get($form::CERTIFICATION)->setValue($user->getCertification());
			$form->get($form::SPECIALIZATION)->setValue($user->getSpecialization());
			$form->get($form::TELEPHONE)->setValue($user->getTelephone());
			$form->get($form::HOURS)->setValue($user->getHours());
		}

		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				$now = new \DateTime();

				// Update the values
				$user->setDisplayNameOrder($form->get($form::DISPLAY_NAME_ORDER)->getValue());
				$user->setEmail($form->get($form::EMAIL)->getValue());
				$user->setFirstName($form->get($form::FIRST_NAME)->getValue());
				$user->setGender($form->get($form::GENDER)->getValue());
				$user->setLastName($form->get($form::LAST_NAME)->getValue());
				$user->setMiddleName($form->get($form::MIDDLE_NAME)->getValue());
				$user->setUserType($form->get($form::TYPE)->getValue());

				$user->setLastUpdateDate($now);
				$user->setDisplayName($user->initDisplayName());

				if(($user->getUserType() == Traveloti::AGENT_TYPE)
					|| ($user->getUserType() == Traveloti::BLOGGER_TYPE)) {
					
					$since = $form->get($form::SINCE)->getValue();
					$since = \DateTime::createFromFormat('Y', $since);

					$user->setSince($since);
					$user->setExpertName($form->get($form::EXPERT_NAME)->getValue());
					$user->setAddress1($form->get($form::ADDRESS1)->getValue());
					$user->setAddress2($form->get($form::ADDRESS2)->getValue());
					$user->setCity($form->get($form::CITY)->getValue());
					$user->setRegion($form->get($form::REGION)->getValue());
					$user->setPostalCode($form->get($form::POSTAL_CODE)->getValue());
					$user->setBio($form->get($form::BIO)->getValue());
					$user->setTestimonial($form->get($form::TESTIMONIAL)->getValue());
					$user->setBlogLink($form->get($form::LINK_BLOG)->getValue());
					$user->setFacebookLink($form->get($form::LINK_FACEBOOK)->getValue());
					$user->setLinkedinLink($form->get($form::LINK_LINKEDIN)->getValue());
					$user->setTwitterLink($form->get($form::LINK_TWITTER)->getValue());
					$user->setWebsite($form->get($form::LINK_WEBSITE)->getValue());
					$user->setYoutubeLink($form->get($form::LINK_YOUTUBE)->getValue());
				}
				if($user->getUserType() == Traveloti::AGENT_TYPE) {
					$user->setCertification($form->get($form::CERTIFICATION)->getValue());
					$user->setSpecialization($form->get($form::SPECIALIZATION)->getValue());
					$user->setTelephone($form->get($form::TELEPHONE)->getValue());
					$user->setHours($form->get($form::HOURS)->getValue());
				}

				// Persist the changes
				$em->persist($user);
				$em->flush();

				return $this->redirect()->toRoute('base');
			}
		}

		$content = new ViewModel(array('userForm' => $form));
		$content->setTemplate('base/settings/general');

		// Fetch pagelets
		$welcomeBox = $this->getWelcomeBoxViewModel();
		$navigation = $this->getSidebarNavViewModel('general', true);

		// Construct container view model
		$view = new ViewModel(array('headerTitle' => 'Settings'));
		$view->setTemplate('base/settings/index');
		$view->addChild($welcomeBox, 'pageletWelcomeBox');
		$view->addChild($navigation, 'pageletNavigation');
		$view->addChild($content, 'settingsContent');

		return $this->getView()->addChild($view);
	}
}
?>