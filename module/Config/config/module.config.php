<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Config
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Config;

return array(
	'controllers' => array(
		'invokables' => array(
			'Config\Controller\Index' => 'Config\Controller\IndexController',
		),
	),
	
	'router' => array(
		'routes' => array(
			'config' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/cfg[/:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '([a-zA-Z0-9_-]+\.?[a-z]?)?',
					),
					'defaults' => array(
						'controller' => 'Config\Controller\Index',
						'action'     => 'index',
					),
				),
			),
		),
	),
	
	'view_manager' => array(
		'template_path_stack' => array(
			'config' => __DIR__ . '/../view',
		),
	),
	
	'view_helpers' => array(
		'invokables' => array(
			'htmlImg' => 'Application\View\Helper\ImgElement',
			'comment' => 'Application\View\Helper\Comment',
			'elapsedTime' => 'Application\View\Helper\ElapsedTime',
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