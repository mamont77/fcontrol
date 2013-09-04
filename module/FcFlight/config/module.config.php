<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcFlight\Controller\Flight' => 'FcFlight\Controller\FlightController',
            'FcFlight\Controller\Leg' => 'FcFlight\Controller\LegController',
            'FcFlight\Controller\Refuel' => 'FcFlight\Controller\RefuelController',
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
                'type' => 'Literal',
                'options' => array(
                    'route' => '/flights/search',
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
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
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
