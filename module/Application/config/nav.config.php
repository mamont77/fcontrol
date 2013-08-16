<?php
return array(
    'navigation' => array(
        'fcontrol' => array(
            array(
                'label' => 'Flights',
                'route' => 'home',
            ),
            array(
                'label' => 'Libraries',
                'type' => 'uri',
                'pages' => array(
                    array(
                        'label' => 'Regions',
                        'route' => 'zfcadmin/regions',
                    ),
                    array(
                        'label' => 'Countries',
                        'route' => 'zfcadmin/countries',
                    ),
                    array(
                        'label' => 'Cities',
                        'route' => 'zfcadmin/cities',
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
                    array(
                        'label' => 'Units',
                        'route' => 'zfcadmin/units',
                    ),
                    array(
                        'label' => 'Currencies',
                        'route' => 'zfcadmin/currencies',
                    ),
                    array(
                        'type'      => 'uri',
                        'divider'   => true,
                    ),
                    array(
                        'label' => 'Advanced search',
                        'route' => 'zfcadmin/advanced_search',
                    ),
                ),
            ),
            array(
                'label' => 'Users',
                'route' => 'zfcadmin/users',
                'action' => 'index',
            ),
        ),
    ),
);
