<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcFlight\Controller\Flight' => 'FcFlight\Controller\FlightController',
            'FcFlight\Controller\Leg' => 'FcFlight\Controller\LegController',
            'FcFlight\Controller\Refuel' => 'FcFlight\Controller\RefuelController',
            'FcFlight\Controller\Permission' => 'FcFlight\Controller\PermissionController',
            'FcFlight\Controller\ApService' => 'FcFlight\Controller\ApServiceController',
            'FcFlight\Controller\Search' => 'FcFlight\Controller\SearchController',
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'FcFlight\Controller\Plugin\CommonData' => 'FcFlight\Controller\Plugin\CommonData',
            'FcFlight\Controller\Plugin\LogPlugin' => 'FcFlight\Controller\Plugin\LogPlugin',
        ),
    ),

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
                        'action' => 'active',
                    ),
                ),
            ),
            'flightsActive' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/flights/active[/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
                        'action' => 'active',
                    ),
                ),
            ),
            'flightsArchived' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/flights/archived[/page/:page][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
                        'action' => 'archived',
                    ),
                ),
            ),
            'flightsSearch' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/flights/search',
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Search',
                        'action' => 'searchResult',
                    ),
                ),
            ),
            'flight' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/flight[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
                        'action' => 'add',
                    ),
                ),
            ),
            'browse' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/browse[/:refNumberOrder]',
                    'constraints' => array(
                        'action' => 'show',
                        'refNumberOrder' => 'ORD-[0-9]{6,6}-[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
                        'action' => 'show',
                    ),
                ),
            ),
            'leg' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/leg[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Leg',
                        'action' => 'index',
                    ),
                ),
            ),
            'refuel' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/refuel[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Refuel',
                        'action' => 'index',
                    ),
                ),
            ),
            'permission' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/permission[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Permission',
                        'action' => 'index',
                    ),
                ),
            ),
            'apService' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/ap-service[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\ApService',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view/',
        ),
    ),
);
