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
                            'incoming-invoice-step1' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/incoming-invoice-step1',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomingInvoiceStep1'
                                    )
                                ),
                            ),
                            'incoming-invoice-step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/incoming-invoice-step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomingInvoiceStep2'
                                    )
                                ),
                            ),
                            'incoming-invoice-step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/incoming-invoice-step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomingInvoiceStep3'
                                    )
                                ),
                            ),
                            'incoming-invoice' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '[/:action][/:id]',
                                    'constraints' => array(
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'add'
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
