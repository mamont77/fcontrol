<?php
return array(
    'navigation' => array(
        'fcontrol' => array(
            array(
                'label' => 'Control panel',
                'type' => 'uri',
                'pages' => array(
                    array(
                        'label' => 'Users',
                        'route' => 'zfcadmin/users',
                        'action' => 'index',
                    ),
                    array(
                        'type' => 'uri',
                        'divider' => true,
                    ),
                    array(
                        'label' => 'Libraries',
                        'type' => 'uri',
                        'navHeader' => true,
                    ),
                    array(
                        'label' => 'Regions',
                        'route' => 'zfcadmin/regions',
                    ),
                    array(
                        'label' => 'Countries',
                        'route' => 'zfcadmin/countries',
                    ),
                    array(
                        'label' => 'Airports',
                        'route' => 'zfcadmin/airports',
                    ),
                    array(
                        'label' => 'Types Aircraft',
                        'route' => 'zfcadmin/aircraft_types',
                    ),
                    array(
                        'label' => 'Aircrafts',
                        'route' => 'zfcadmin/aircrafts',
                    ),
                    array(
                        'label' => 'Air Operators',
                        'route' => 'zfcadmin/air_operators',
                    ),
                    array(
                        'label' => 'Kontragents',
                        'route' => 'zfcadmin/kontragents',
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
