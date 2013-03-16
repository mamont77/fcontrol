<?php
return array(
    'navigation' => array(
        'fcontrol' => array(
            array(
                'label' => 'Панель управления',
                'type' => 'uri',
                'pages' => array(
                    array(
                        'label' => 'Пользователи',
                        'route' => 'zfcadmin/users',
                        'action' => 'index',
                    ),
                    array(
                        'type' => 'uri',
                        'divider' => true,
                    ),
                    array(
                        'label' => 'Библиотека',
                        'type' => 'uri',
                        'navHeader' => true,
                    ),
                    array(
                        'label' => 'Регионы',
                        'route' => 'zfcadmin/regions',
                    ),
                    array(
                        'label' => 'Страны',
                        'route' => 'zfcadmin/countries',
                    ),
                    array(
                        'label' => 'Аэропорты',
                        'route' => 'zfcadmin/airports',
                    ),
                ),
            ),
            array(
                'label' => 'Links',
                'title' => 'Resources utilized by DluTwBootstrap',
                'type' => 'uri',
                'pages' => array(
                    array(
                        'label' => 'Tutorials & Discussion',
                        'type' => 'uri',
                        'navHeader' => true,
                    ),
                    array(
                        'label' => 'DluTwBootstrap on ZF Daily blog',
                        'uri' => 'http://www.zfdaily.com/tag/dlutwbootstrap/',
                    ),
                    array(
                        'type' => 'uri',
                        'divider' => true,
                    ),
                    array(
                        'label' => 'Git Repository on Bitbucket',
                        'type' => 'uri',
                        'navHeader' => true,
                    ),
                    array(
                        'label' => 'DluTwBootstrap (ZF2 module)',
                        'uri' => 'https://bitbucket.org/dlu/dlutwbootstrap',
                    ),
                    array(
                        'label' => 'DluTwBootstrap Demo (ZF2 module)',
                        'uri' => 'https://bitbucket.org/dlu/dlutwbootstrap-demo',
                    ),
                    array(
                        'label' => 'DluTwBootstrap Demo App (ZF2 application)',
                        'uri' => 'https://bitbucket.org/dlu/dlutwbootstrap-demo-app',
                    ),
                    array(
                        'type' => 'uri',
                        'divider' => true,
                    ),
                    array(
                        'label' => 'Twitter Bootstrap',
                        'type' => 'uri',
                        'navHeader' => true,
                    ),
                    array(
                        'label' => 'Forms',
                        'uri' => 'http://twitter.github.com/bootstrap/base-css.html#forms',
                    ),
                ),
            ),
        ),
    ),
);
