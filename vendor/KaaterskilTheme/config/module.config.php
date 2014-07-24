<?php
/**
 * Kaaterskil Library
 *
 * @package		KaaterskilTheme
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

return array(
	'kaaterskil_theme' => array(
		'default_theme' => null,
		
		'theme_paths' => array(
			dirname(__DIR__) . '/themes/',
		),
		
		'adapters' => array(
			'KaaterskilTheme\Adapter\Configuration',
		),
	),
);
?>