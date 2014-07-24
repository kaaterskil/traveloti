<?php
return array(
    'template_path_stack' => array(
        'default' => __DIR__ . '/view',
    ),
	'template_map' => array(
		'application/index/index'			=> __DIR__ . '/view/application/index/index.phtml',
		'application/index/login'			=> __DIR__ . '/view/application/index/login.phtml',
		'application/index/pagelet_topbar'	=> __DIR__ . '/view/application/index/pagelet_topbar.phtml',
		'application/index/registration'	=> __DIR__ . '/view/application/index/registration.phtml',
		'zfc-user/user/login'				=> __DIR__ . '/view/zfc-user/user/login.phtml',
		'zfc-user/user/register'			=> __DIR__ . '/view/zfc-user/user/register.phtml',
		'layout/layout'						=> __DIR__ . '/view/layout/layout.phtml',
		'error/404'							=> __DIR__ . '/view/error/404.phtml',
		'error/index'						=> __DIR__ . '/view/error/index.phtml',
	),
);
?>