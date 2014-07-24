<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Service\OpenfireUserService;
use Application\Form\Register as TravelotiRegister;
use Base\Model\Traveloti;
use Doctrine\ORM\Query\ResultSetMapping;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Form\Form;
use Zend\Form\Element;
use ZfcUser\Form\Register as ZfcRegister;

class Module {

	public function onBootstrap(MvcEvent $e) {
		$sm = $e->getApplication()->getServiceManager();
		$em = $e->getApplication()->getEventManager();

		// For Translation
		$e->getApplication()->getServiceManager()->get('translator');
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($em);


		// Listener added by Blair to initialize the ZfcUser Registration Form
		$events = $em->getSharedManager();
		$events->attach('ZfcUser\Form\Register','init', function($e) {
			$form = $e->getTarget();
			$this->initRegistrationFormElements($form);
		});

		// Listener added by Blair to further prepare a new User before insert.
		$events->attach('Application\Service\ZfcUser', 'register', function($e) {
			$service = $e->getTarget();
			$user = $e->getParam('user');
			$service->preInsert($user);
		});
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	/**
	 * Added by Blair
	 *
	 * @see http://stackoverflow.com/questions/11355126/how-do-i-get-the-service-manager-in-zend-framework-2-beta4-to-create-an-instance
	 * @return multitype
	 */
	public function getServiceConfig() {
		return array(
			'invokables' => array(
                'zfcuser_user_service' => 'Application\Service\ZfcUser',
			),
			'factories' => array(
				'db-adapter' => function($sm) {
					$config = $sm->get('config');
					$config = $config['db'];
					$dbAdapter = new DbAdapter($config);
					return $dbAdapter;
				},
				
				'OpenfireUserService' => function($sm) {
					$config = $sm->get('config');
					if((!isset($config['openfire']))
							|| (!$config['openfire']['enabled'])) {
						return null;
					}
						
					$config = $config['openfire'];
					$mode = $config['mode'];
					$host = $config['host'];
					$secret = $config['secret']; // Requires Openfire UserService plugin
					$openfireAdapter = new OpenfireUserService($sm, $mode, $host, $secret);
					return $openfireAdapter;
				},
			),
		);
	}

	/**
	 * Added by Blair
	 * @param Form $form
	 */
	private function initRegistrationFormElements(ZfcRegister $form) {
		// Remove ZfcUser fields
		$form->remove('username');
		$form->remove('display_name');
		$form->remove('submit');
			
		// Add Traveloti fields
		$firstName = new Element\Text();
		$firstName->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text text-field',
			'name' => 'firstName',
			'placeholder' => 'First Name',
		));
		$form->add($firstName);
		
		$lastName = new Element\Text();
		$lastName->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text text-field',
			'name' => 'lastName',
			'placeholder' => 'Last Name',
		));
		$form->add($lastName);

		$userType = new Element\Select();
		$userType->setAttributes(array(
			'name' => 'userType',
			'value' => 'Traveler',
		));
		$userType->setValueOptions(array(
			Traveloti::USER_TYPE => 'Traveler',
			Traveloti::BLOGGER_TYPE => 'Writer/Blogger',
			Traveloti::AGENT_TYPE => 'Agent',
		));
		$form->add($userType);

		$gender = new Element\Radio();
		$gender->setAttributes(array(
			'class' => 'sex-option input-label-radio',
			'name' => 'gender',
			'value' => '0',
		));
		$gender->setOptions(array(
			'use_hidden_element' => true,
			'unchecked_value' => '0',
			'value_options' => array(
				'0' => 'Female',
				'1' => 'Male',
			),
			'label_attributes' => array(
				'class' => 'input-label clearfix inline-block gender-label',
			),
		));
		$form->add($gender);

		$creationDate = new Element\Hidden();
		$creationDate->setAttributes(array(
			'name' => 'creationDate',
			'value' => date('Y-m-d H:i:s', time()),
		));
		$form->add($creationDate);

		$submit = new Element\Submit();
		$submit->setAttributes(array(
			'class' => '_6j mvm _6wl _6wk signup-button _3m8 _6o _6v',
			'name' => 'submit_btn',
			'value' => 'Continue',
		));
		$form->add($submit);

		// Adjust the attributes and options to existing ZfcUser fields
		$form->get('email')->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text text-field',
			'placeholder' => 'Your Email',
		));
		$form->get('password')->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text text-field',
			'placeholder' => 'New Password',
		));
		$form->get('passwordVerify')->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text text-field',
			'placeholder' => 'Re-enter Password',
		));
		$form->get('submit_btn')->setOptions(array(
			'label' => '',
			'label_attributes' => array(),
		));

		$form->setValidationGroup(
			'firstName',
			'lastName',
			'userType',
			'gender',
			'email',
			'password',
			'passwordVerify'
		);
	}
}
