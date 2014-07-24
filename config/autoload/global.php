<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
	'db' => array(
		'driver'         => 'Pdo',
		'dsn'            => 'mysql:dbname=traveloti;host=localhost',
		'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
	),

	'service_manager' => array(
		'factories' => array(
			'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
		),
	),
	
	'module_layouts' => array(
		'Application' => 'layout/layout',
		'Base' => 'layout/base_layout',
	),
	
	'kaaterskil_theme' => array(
	    'default_theme' => 'traveloti',
	    'theme_paths' => array(
	        dirname(dirname(__DIR__)) . '/module/Application/themes/',
	        dirname(dirname(__DIR__)) . '/module/Base/themes/',
	    ),
	    'adapters' => array(
	        'KaaterskilTheme\Adapter\Configuration',
	        'KaaterskilTheme\Adapter\Configuration',
	    ),
	),
);
