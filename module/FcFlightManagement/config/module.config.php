<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcFlightManagement\Controller\Refuel' => 'FcFlightManagement\Controller\RefuelController',
            'FcFlightManagement\Controller\ApService' => 'FcFlightManagement\Controller\ApServiceController',
            'FcFlightManagement\Controller\Permission' => 'FcFlightManagement\Controller\PermissionController',
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
                            'outcome-invoice-print' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/outcome-invoice-print[/:id]',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Refuel',
                                        'action' => 'outcomeInvoicePrint'
                                    )
                                ),
                            ),
                        )
                    ),
                    'ap-service' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/ap-service',
                            'defaults' => array(
                                'controller' => 'FcFlightManagement\Controller\ApService',
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
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'incomeInvoiceStep1'
                                    )
                                ),
                            ),
                            'income-invoice-step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'incomeInvoiceStep2'
                                    )
                                ),
                            ),
                            'income-invoice-step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'incomeInvoiceStep3'
                                    )
                                ),
                            ),
                            'income-invoice-add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-add',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
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
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'incomeInvoiceShow'
                                    )
                                ),
                            ),
                            'outcome-invoice-step1' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step1',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'outcomeInvoiceStep1'
                                    )
                                ),
                            ),
                            'outcome-invoice-step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'outcomeInvoiceStep2'
                                    )
                                ),
                            ),
                            'outcome-invoice-step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'outcomeInvoiceStep3'
                                    )
                                ),
                            ),
                            'outcome-invoice-add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-add',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
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
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'outcomeInvoiceShow'
                                    )
                                ),
                            ),
                            'outcome-invoice-print' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/outcome-invoice-print[/:id]',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\ApService',
                                        'action' => 'outcomeInvoicePrint'
                                    )
                                ),
                            ),
                        )
                    ),
                    'permission' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/permission',
                            'defaults' => array(
                                'controller' => 'FcFlightManagement\Controller\Permission',
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
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'incomeInvoiceStep1'
                                    )
                                ),
                            ),
                            'income-invoice-step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'incomeInvoiceStep2'
                                    )
                                ),
                            ),
                            'income-invoice-step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'incomeInvoiceStep3'
                                    )
                                ),
                            ),
                            'income-invoice-add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/income-invoice-add',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
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
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'incomeInvoiceShow'
                                    )
                                ),
                            ),
                            'outcome-invoice-step1' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step1',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'outcomeInvoiceStep1'
                                    )
                                ),
                            ),
                            'outcome-invoice-step2' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step2',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'outcomeInvoiceStep2'
                                    )
                                ),
                            ),
                            'outcome-invoice-step3' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-step3',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'outcomeInvoiceStep3'
                                    )
                                ),
                            ),
                            'outcome-invoice-add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/outcome-invoice-add',
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
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
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'outcomeInvoiceShow'
                                    )
                                ),
                            ),
                            'outcome-invoice-print' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/outcome-invoice-print[/:id]',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'FcFlightManagement\Controller\Permission',
                                        'action' => 'outcomeInvoicePrint'
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
