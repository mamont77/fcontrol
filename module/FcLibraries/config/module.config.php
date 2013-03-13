<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcLibraries\Controller\Index' => 'FcLibraries\Controller\IndexController',
            'FcLibraries\Controller\Regions' => 'FcLibraries\Controller\RegionsController',
            'FcLibraries\Controller\Countries' => 'FcLibraries\Controller\CountriesController',
            'FcLibraries\Controller\Airports' => 'FcLibraries\Controller\AirportsController',
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
                                'controller' => 'FcLibraries\Controller\Regions',
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
                        ),
                    ),
                    'countries' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/libraries/countries',
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Countries',
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
                        ),
                    ),
                    'airports' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/libraries/airports',
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Airports',
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
