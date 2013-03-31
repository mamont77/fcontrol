<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcLibraries\Controller\Index' => 'FcLibraries\Controller\IndexController',
            'FcLibraries\Controller\Region' => 'FcLibraries\Controller\RegionController',
            'FcLibraries\Controller\Country' => 'FcLibraries\Controller\CountryController',
            'FcLibraries\Controller\Airport' => 'FcLibraries\Controller\AirportController',
        ),
    ),


    'router' => array(
        'routes' => array(
            'zfcadmin' => array(
                'child_routes' => array(
                    'libraries' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/libraries',
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Index',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'regions' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/regions[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Region',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'region' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/region[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Region',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'countries' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/countries[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Country',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'country' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/country[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Country',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'airports' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/airports[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Airport',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'airport' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/airport[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Airport',
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
