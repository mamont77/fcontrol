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
                            'income-invoice-step1' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-step1',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomeInvoiceStep1'
                                    )
                                ),
                            ),
                            'income-invoice-step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomeInvoiceStep2'
                                    )
                                ),
                            ),
                            'income-invoice-step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomeInvoiceStep3'
                                    )
                                ),
                            ),
                            'income-invoice-add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-add',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomeInvoiceAdd'
                                    )
                                ),
                            ),
                            'income-invoice-show' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/income-invoice-show[/:id]',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'incomeInvoiceShow'
                                    )
                                ),
                            ),
                            'outcome-invoice-step1' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step1',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'outcomeInvoiceStep1'
                                    )
                                ),
                            ),
                            'outcome-invoice-step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'outcomeInvoiceStep2'
                                    )
                                ),
                            ),
                            'outcome-invoice-step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'outcomeInvoiceStep3'
                                    )
                                ),
                            ),
                            'outcome-invoice-add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-add',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'outcomeInvoiceAdd'
                                    )
                                ),
                            ),
                            'outcome-invoice-show' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/outcome-invoice-show[/:id]',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'outcomeInvoiceShow'
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
