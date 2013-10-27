<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcLogEvents\Controller\Index' => 'FcLogEvents\Controller\IndexController',
            'FcLogEvents\Controller\Search' => 'FcLogEvents\Controller\SearchController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'logs' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/logs/list[/page/:page][/order_by/:order_by][/:order]',
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
            'logsSearch' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/logs/search',
                    'defaults' => array(
                        'controller' => 'FcLogEvents\Controller\Search',
                        'action' => 'searchResult',
                    ),
                ),
            ),
            'cron' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/logs/cron',
                    'defaults' => array(
                        'controller' => 'FcLogEvents\Controller\Index',
                        'action' => 'cron',
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
