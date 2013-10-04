<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcLibraries\Controller\Aircraft' => 'FcLibraries\Controller\AircraftController',
            'FcLibraries\Controller\AircraftType' => 'FcLibraries\Controller\AircraftTypeController',
            'FcLibraries\Controller\AirOperator' => 'FcLibraries\Controller\AirOperatorController',
            'FcLibraries\Controller\Airport' => 'FcLibraries\Controller\AirportController',
            'FcLibraries\Controller\BaseOfPermit' => 'FcLibraries\Controller\BaseOfPermitController',
            'FcLibraries\Controller\City' => 'FcLibraries\Controller\CityController',
            'FcLibraries\Controller\Country' => 'FcLibraries\Controller\CountryController',
            'FcLibraries\Controller\Currency' => 'FcLibraries\Controller\CurrencyController',
            'FcLibraries\Controller\Index' => 'FcLibraries\Controller\IndexController',
            'FcLibraries\Controller\Kontragent' => 'FcLibraries\Controller\KontragentController',
            'FcLibraries\Controller\Region' => 'FcLibraries\Controller\RegionController',
            'FcLibraries\Controller\Unit' => 'FcLibraries\Controller\UnitController',
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'FcLibraries\Controller\Plugin\CommonData' => 'FcLibraries\Controller\Plugin\CommonData',
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
                    'aircrafts' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/aircrafts[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Aircraft',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'aircraft' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/aircraft[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Aircraft',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'aircraft_types' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/aircraft_types[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\AircraftType',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'aircraft_type' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/aircraft_type[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\AircraftType',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'air_operators' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/air_operators[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\AirOperator',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'air_operator' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/air_operator[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\AirOperator',
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
                    'base_of_permits' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/base_of_permits[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\BaseOfPermit',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'base_of_permit' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/base_of_permit[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\BaseOfPermit',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'cities' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/cities[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\City',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'city' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/city[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\City',
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
                    'currencies' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/currencies[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Currency',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'currency' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/currency[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Currency',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'kontragents' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/kontragents[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Kontragent',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'kontragent' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/kontragent[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Kontragent',
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
                    'units' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/units[/page/:page][/order_by/:order_by][/:order]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                                'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'order' => 'ASC|DESC',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Unit',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'unit' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/unit[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibraries\Controller\Unit',
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
