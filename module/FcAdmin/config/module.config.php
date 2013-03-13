<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'FcAdmin\Controller\Index' => 'FcAdmin\Controller\IndexController',
            'FcAdmin\Controller\User' => 'FcAdmin\Controller\UserController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'zfcadmin' => array(
                'options' => array(
                    'route' => '/admin',
                    'type' => 'literal',
                    'defaults' => array(
                        'controller' => 'FcAdmin\Controller\Index',
                        'action' => 'dashboard',
                    ),
                ),
                'child_routes' => array(
                    'users' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/users',
                            'defaults' => array(
                                'controller' => 'FcAdmin\Controller\User',
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
                            'defaults' => array(
                                'controller' => 'FcAdmin\Controller\User',
                                'action' => 'index',
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
    'translator' => array(
      'locale' => 'ru_RU',
      'translation_file_patterns' => array(
        array(
          'type'     => 'gettext',
          'base_dir' => __DIR__ . '/../language',
          'pattern'  => '%s.mo',
          'text_domain' => __NAMESPACE__,
        ),
      ),
    ),

    'zfcadmin' => array(
        'use_admin_layout' => false,
        'admin_layout_template' => 'layout/admin', //TODO remove it
    ),
);
