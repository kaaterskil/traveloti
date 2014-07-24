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

use Base\Controller\Plugin\FriendFinder;
use Base\Form\FriendFinderForm;
use Base\Form\SearchForm;
use Zend\View\Model\ViewModel;

/**
 * FriendController Class
 * @author Blair
 */
class FindFriendsController extends AbstractBaseController {

	/**
	 * Find friends page
	 * @return \Zend\View\Model\ViewModel
	 */
	public function browserAction() {
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();
		
		// Construct form
		$collection = $em->getRepository('Config\Model\Profile')->findAll();
		usort($collection, function($a, $b){
			return strcmp(
				$a->getCategory()->getDisplayName() . $a->getDisplayName(),
				$b->getCategory()->getDisplayName() . $b->getDisplayName()
			);
		});
		$interests = array();
		foreach ($collection as $interest){
			$category = $interest->getCategory()->getDisplayName();
			if(in_array($category, $interests)) {
				$interests[$category] = array();
			}
			$interests[$category][] = $interest;
		}
		$pf = new FriendFinderForm($interests);
		
		// Fetch friends
		$request = $this->getRequest();
		if($request->isPost()) {
			$users = array();
			$pf->setData($request->getPost());
			foreach ($pf->getElements() as $element) {
				if(($element->getName() != 'traveloti_id') && ($element->getValue() != null)) {
					$profileId = $element->getValue();
					$profile = $em->getRepository('Config\Model\Profile')->find($profileId);
					$users = $this->friendFInder()->findTraveloti($profile);
					break;
				}
			}
		} else {
			$users = $this->friendFinder()->findFriends();
		}

		// Construct friend browser view model
		$browser = new ViewModel(array('travelotis' => $users, 'interestsForm' => $pf));
		$browser->setTemplate('base/find-travelotis/browser');

		return $this->getView('full-page')->addChild($browser);
	}
	
	/**
	 * Full search page
	 */
	public function searchAction() {
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}
		
		$traveloti = array();
		$request = $this->getRequest();
		if($request->isPost()) {
			$form = new SearchForm();
			$form->setData($request->getPost());
			if($form->isValid()) {
				$queryString = $form->get('query')->getValue();
				$traveloti = $this->friendFinder()->search($queryString);
			}
		}
		
		// Construct view model
		$vm = new ViewModel(array('searchResults' => $traveloti));
		$vm->setTemplate('base/find-travelotis/search');
		return $this->getView('full-page')->addChild($vm);
	}
}
?>