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
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/libraries/regions',
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
                                'action' => 'add',
                            ),
                        ),
                    ),
                    'countries' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/libraries/countries',
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
                                'action' => 'add',
                            ),
                        ),
                    ),
                    'airports' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/libraries/airports',
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
                                'action' => 'add',
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
    ),
    'translator' => array(
        'locale' => 'ru_RU',
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
