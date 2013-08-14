<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcLibrariesSearch\Controller\Search' => 'FcLibrariesSearch\Controller\SearchController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'zfcadmin' => array(
                'child_routes' => array(
                    'advanced_search' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/libraries/advanced_search[/:action][/:string]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'string' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'FcLibrariesSearch\Controller\Search',
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
    ),
);
