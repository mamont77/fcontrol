<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
            'album' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/album[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Album',
                        'action'     => 'index',
                    ),
                ),
            ),
//            'zfcadmin' => array(
//                'child_routes' => array(
//                    'album' => array(
//                        'type' => 'Literal',
//                        'options' => array(
//                            'route' => 'admin/album',
//                            'defaults' => array(
//                                'controller' => 'Album\Controller\Album',
//                                'action'     => 'index',
//                            ),
//                        ),
//                        'child_routes' =>array(
//                            'mychildroute' => array(
//                                'type' => 'literal',
//                                'options' => array(
//                                    'route' => '/',
//                                    'defaults' => array(
//                                        'controller' => 'Album\Controller\Album',
//                                        'action'     => 'edit',
//                                        'id'     => '[0-9]+',
//                                    ),
//                                ),
//                            ),
//                        ),
//                    ),
//                ),
//            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);
