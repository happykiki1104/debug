<?php
return array(
	'debugconfig' => array(
		'enable' => true,//true=>write cache file
		'cachepath' => './data/cache/debug.fengruzhuo.cache',
	),
    'router' => array(
        'routes' => array(
            'debug' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/debug',
                    'defaults' => array(
                         '__NAMESPACE__' => 'FengruzhuoDebug\Controller',
                        'controller'    => 'IndexController',
                        'action'     => 'index',
                    ),
                ),
            ),
		),
	),
    'controllers' => array(
        'invokables' => array(
            'FengruzhuoDebug\Controller\IndexController' => 'FengruzhuoDebug\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
//		'layout' => 'debug/layout',
        'template_path_stack' => array(
            'FengruzhuoDebug' => __DIR__ . '/../view',
        ),
		'template_map' => array(
			'debug/layout'             => __DIR__ . '/../view/layout/layout.phtml',
		),
    ),
    'service_manager' => array(
		'aliases' => array(
			'fengruzhuo_debug_db_adapter' => 'Zend\Db\Adapter\Adapter',
		),
        'factories' => array(
        ),
	),
);

