<?php
/**
 * Kaaterskil Library
 *
 * @package		KaaterskilTheme
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace KaaterskilTheme;

use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

/**
 * Module Class
 * @author Blair
 */
class Module {
	
	/**
	 * Called when the ModuleManager starts up
	 * @param ModuleManager $mgr
	 */
	public function init(ModuleManager $mgr) {
		$mgr->getEventManager()->getSharedManager()->attach(
			'Zend\Mvc\Application',
			'bootstrap',
			array($this, 'bootstrap'),
			10001
		);
	}
	
	/**
	 * Sets the ServieManaager instance so we can use it in the module
	 * @param MvcEvent $e
	 */
	public function bootstrap(MvcEvent $e) {
		$sm = $e->getApplication()->getServiceManager();
		$themeMgr = $sm->get('KaaterskilThemeManager');
		$themeMgr->init();
		
		// Set the controller layout
		$e->getApplication()->getEventManager()->getSharedManager()->attach(
		  	'Zend\Mvc\Controller\AbstractActionController',
			'dispatch',
			function($e) {
				$controller = $e->getTarget();
				$clazz = get_class($controller);
				$ns = substr($clazz, 0, strpos($clazz, '\\'));
				
				$config = $e->getApplication()->getServiceManager()->get('config');
				if(isset($config['module_layouts'][$ns])) {
					$controller->layout($config['module_layouts'][$ns]);
				}
			}, 100
		);
	}
	
	/**
	 * Returns the core configuration array
	 * @return array
	 */
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	
	/**
	 * Returns the Autoloader config
	 * @return array
	 */
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Returns the Service Configuration
     * @return array
     */
	public function getServiceConfig() {
		return array(
			'factories' => array(
				'KaaterskilThemeManager' => 'KaaterskilTheme\Service\ManagerFactory'
			)
		);
	}
}
?>