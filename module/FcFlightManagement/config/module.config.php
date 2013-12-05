<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcFlightManagement\Controller\Refuel' => 'FcFlightManagement\Controller\RefuelController',
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(),
    ),

    'router' => array(
        'routes' => array(
            'management' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/management',
                    'defaults' => array(
                        'controller' => 'FcFlightManagement\Controller\Refuel',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'refuel' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/refuel',
                            'defaults' => array(
                                'controller' => 'FcFlightManagement\Controller\Refuel',
                                'action' => 'index'
                            )
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'step1' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/step1',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'step1'
                                    )
                                ),
                            ),
                            'step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'step2'
                                    )
                                ),
                            ),
                            'step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'step3'
                                    )
                                ),
                            ),
                        )
                    ),
                )
            )
        )
    ),


    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
