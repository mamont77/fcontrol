<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcLogEvents\Controller\Index' => 'FcLogEvents\Controller\IndexController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'zfcadmin' => array(
                'child_routes' => array(
                    'logs' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/logs[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLogEvents\Controller\Index',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'libraries-pagination-slide' => __DIR__ . '/../view/layout/slidePagination.phtml',
        ),
    ),
    'translator' => array(
//        'locale' => 'ru_RU',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => __NAMESPACE__,
            ),
        ),
    ),
);
