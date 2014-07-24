<?php
return array(
    'modules' => array(
    	'ZfcBase',
    	'ZfcUser',
    	'DoctrineModule',
    	'DoctrineORMModule',
    	'ZfcUserDoctrineORM',
    	'ScnSocialAuthDoctrineORM',
    	'ScnSocialAuth',
    	'KaaterskilTheme',
    	'Application',
    	'Base',
    	'Config',
    	'Location',
    ),
	
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    	
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
