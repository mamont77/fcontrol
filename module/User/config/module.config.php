<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
        ),
    ),

    'navigation' => array(

        'admin' => array(
            'home' => array(
                'label' => 'fControl (alfa)',
                'route' => 'home',
                'class' => 'brand',
            ),
            'dashboard' => array(
                'label' => 'Dashboard',
                'route' => 'zfcadmin',
            ),
            'user' => array(
                'label' => 'Управление пользователями',
                'route' => 'zfcadmin/users',
            ),
        ),
    ),

    'router' => array(
        'routes' => array(
            'zfcadmin' => array(
                'options' => array(
                    'route' => '/admin',
                    'type' => 'literal',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action' => 'dashboard',
                    ),
                ),
                'child_routes' => array(
                    'users' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/users',
                            'defaults' => array(
                                'controller' => 'User\Controller\User',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'user' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/user[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'template_path_stack' => array(
            'user' => __DIR__ . '/../view',
        ),
    ),
    'zfcadmin' => array(
        'use_admin_layout' => false,
        'admin_layout_template' => 'layout/admin' //TODO remove it
    ),
);
