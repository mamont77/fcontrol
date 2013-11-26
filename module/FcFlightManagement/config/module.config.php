<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcFlightManagement\Controller\Refuel' => 'FcFlightManagement\Controller\RefuelController',
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(
        ),
    ),

    'router' => array(
        'routes' => array(
            'management' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/management',
                ),
                'child_routes' => array(
                    'refuel' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/refuel/step1',
                            'defaults' => array(
                                'controller' => 'FcFlightManagement\Controller\Refuel',
                                'action' => 'findStep1',
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
);
