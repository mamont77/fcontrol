<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcFlight\Controller\Flight' => 'FcFlight\Controller\FlightController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[/page/:page][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
                        'action' => 'index',
                    ),
                ),
            ),
            'flights' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/flights[/page/:page][/order_by/:order_by][/:order]',
                    'constraints' => array(
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'order' => 'ASC|DESC',
                    ),
                    'defaults' => array(
                        'controller' => 'FcFlight\Controller\Flight',
                        'action' => 'index',
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
