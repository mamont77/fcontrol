<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcFlightManagement\Controller\FlightManagement' => 'FcFlight\Controller\FlightManagementController',
            'FcFlightManagement\Controller\RefuelManagement' => 'FcFlight\Controller\RefuelManagementController',
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(
        ),
    ),

    'router' => array(
        'routes' => array(
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
