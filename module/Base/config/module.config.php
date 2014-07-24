<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base;

return array(
	'controllers' => array(
		'invokables' => array(
			'Base\Controller\Ajax' => 'Base\Controller\AjaxController',
			'Base\Controller\Base' => 'Base\Controller\BaseController',
			'Base\Controller\Friend' => 'Base\Controller\FindFriendsController',
			'Base\Controller\General' => 'Base\Controller\GeneralController',
			'Base\Controller\Log' => 'Base\Controller\LogController',
			'Base\Controller\Profile' => 'Base\Controller\ProfileController',
			'Base\Controller\Notifications' => 'Base\Controller\NotificationsController',
			'Base\Controller\Timeline' => 'Base\Controller\TimelineController',
		),
	),
	
    'controller_plugins' => array(
        'invokables' => array(
            'SideBarGenerator' => 'Base\Controller\Plugin\SideBarGenerator',
            'FriendFinder' => 'Base\Controller\Plugin\FriendFinder',
        	'loginTest' => 'Base\Controller\Plugin\LoginTest',
        ),
    ),
	
	'view_helpers' => array(
		'invokables' => array(
			'basePath' => 'Application\View\Helper\BasePath',
			'comment' => 'Application\View\Helper\Comment',
			'elapsedTime' => 'Application\View\Helper\ElapsedTime',
			'htmlImg' => 'Application\View\Helper\ImgElement',
			'photoSizer' => 'Application\View\Helper\PhotoSizer',
			'notifyIconClass' => 'Application\View\Helper\NotificationIconPicker',
			'navigationInterests' => 'Application\View\Helper\NavigationInterests',
			'navigationTile' => 'Application\View\Helper\NavigationTile',
		),
	),
	
	'router' => array(
		'routes' => array(
			'timeline' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/timeline[/:id][/:action][/:set]',
					'constraints' => array(
						'id' => '([a-zA-Z0-9_-]+\.?[a-zA-Z0-9_-]*)?',
						'action' => '([a-zA-Z]+)?',
						'set' => '([0-9]+)?',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\Timeline',
						'action'     => 'index',
					),
				),
			),
			'base' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/base[/:action][/:id][/:type]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '([a-zA-Z0-9_-]+\.?[a-z]?)?',
						'type'	 => '([a-z]+)?',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\Base',
						'action'     => 'index',
					),
				),
			),
			'ajax' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/ajax[/:action][/uid/:uid][/oid/:oid]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'uid' => '([0-9]+)?',
						'oid' => '([a-zA-Z0-9._-]*)?',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\Ajax',
						'action'     => 'index',
					),
				),
			),
			'photoStream' => array(
				'type'    => 'literal',
				'options' => array(
					'route'    => '/base/photoStream',
					'defaults' => array(
						'controller' => 'Base\Controller\Base',
						'action'     => 'photoStream',
					),
				),
			),
			'friend' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/find-traveloti[/:action][/:fid]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'fid' => '([0-9]*)',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\Friend',
						'action'     => 'browser',
					),
				),
			),
			'general' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/settings/general[/:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '([a-zA-Z0-9_-]+\.?[a-z]?)?',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\General',
						'action'     => 'index',
					),
				),
			),
			'profile' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/settings/profile[/:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '([a-zA-Z0-9_-]+\.?[a-z]?)?',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\Profile',
						'action'     => 'index',
					),
				),
			),
			'notifications' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/settings/notifications[/:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '([a-zA-Z0-9_-]+\.?[a-z]?)?',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\Notifications',
						'action'     => 'index',
					),
				),
			),
			'logs' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/logs[/:action][/:id][/:oid]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
						'oid'     => '[0-9]*',
					),
					'defaults' => array(
						'controller' => 'Base\Controller\Log',
						'action'     => 'index',
					),
				),
			),
		),
	),
	
	'view_manager' => array(
		'template_path_stack' => array(
			'base' => __DIR__ . '/../view',
		),
        'strategies' => array(
           'ViewJsonStrategy',
        ),
	),

    'doctrine' => array(
    	'driver' => array(
    		'traveloti_annotation_driver' => array(
    			'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
    			'cache' => 'array',
    			'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Model')
    		),
    		'orm_default' => array(
    			'drivers' => array(
    				__NAMESPACE__ => 'traveloti_annotation_driver',
    			)
    		)
    	)
    ),
);
?>