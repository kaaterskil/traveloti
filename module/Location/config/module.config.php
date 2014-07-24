<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

return array(
	'controllers' => array(
		'invokables' => array(
			'Location\Controller\Location' => 'Location\Controller\LocationController',
			'Location\Controller\Country' => 'Location\Controller\CountryController',
			'Location\Controller\Search' => 'Location\Controller\SearchController',
		),
	),
	
	'router' => array(
		'routes' => array(
			'location' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/location[/:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Location\Controller\Location',
						'action'     => 'index',
					),
				),
			),
			'country' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/country[/:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Location\Controller\Country',
						'action'     => 'index',
					),
				),
			),
			'search' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/search[/:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Location\Controller\Search',
						'action'     => 'index',
					),
				),
			),
		),
	),
	
	'view_manager' => array(
		'template_path_stack' => array(
			'location' => __DIR__ . '/../view',
			'country' => __DIR__ . '/../view',
		),
	),
);
?>