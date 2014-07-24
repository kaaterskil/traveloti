<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUser\Options\UserControllerOptionsInterface;

/**
 * IndexController class
 * @author Blair
 */
class IndexController extends AbstractActionController {

	private $loginForm;
	private $registrationForm;
	private $options;

	/*----- Getter/Setters -----*/

	public function getLoginForm() {
		if(!$this->loginForm) {
			$lf = $this->getServiceLocator()->get("zfcuser_login_form");
			$this->setLoginForm($lf);
		}
		return $this->loginForm;
	}

	public function setLoginForm(Form $loginForm) {
		$this->loginForm = $loginForm;
		$fm = $this->flashMessenger()->setNamespace("zfcuser_login_form")->getMessages();
		if (isset($fm[0])) {
			$this->loginForm->setMessages(
				array("identity" => array($fm[0]))
			);
		}
	}

	public function getOptions() {
		if(!$this->options instanceof  UserControllerOptionsInterface) {
			$this->setOptions($this->getServiceLocator()->get('zfcuser_module_options'));
		}
		return $this->options;
	}

	public function setOptions(UserControllerOptionsInterface $options) {
		$this->options = $options;
		return $this;
	}

	public function getRegistrationForm() {
		if(!$this->registrationForm) {
			$rf = $this->getServiceLocator()->get("zfcuser_register_form");
			$this->setRegistrationForm($rf);
		}
		return $this->registrationForm;
	}

	public function setRegistrationForm(Form $registrationForm) {
		$this->registrationForm = $registrationForm;
	}

	/*----- Methods -----*/

	public function indexAction() {
		if ($this->zfcUserAuthentication()->hasIdentity()) {
			return $this->redirect()->toRoute('base/index');
		}

		$request = $this->getRequest();
		$loginForm = $this->getLoginForm();
		if($request->isPost()){
			$loginForm->setData($request->getPost());
			if($loginForm->isValid()){
				return $this->forward()->dispatch(
					'zfcuser', array('action' => 'authenticate'));
			}
		}
		 
		$registrationForm = $this->getRegistrationForm();
		 
		// Set nested view model for login form
		$lfvm = new ViewModel(array("loginForm" => $loginForm));
		$lfvm->setTemplate("application/index/login");
		 
		$topBarPagelet = new ViewModel();
		$topBarPagelet->setTemplate('application/index/pagelet_topbar');
		$topBarPagelet->addChild($lfvm, "loginForm");
		 
		// Set nested view model for registration form
		$rfvm = new ViewModel(array("registrationForm" => $registrationForm));
		$rfvm->setTemplate("application/index/registration");
		 
		$view = new ViewModel();
		$view->addChild($lfvm, "loginForm")
		->addChild($rfvm, 'registrationForm')
		->addChild($topBarPagelet, 'topBarPagelet');
		return $view;
	}
}
