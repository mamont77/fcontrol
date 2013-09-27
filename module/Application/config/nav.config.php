<?php
return array(
    'navigation' => array(
        'fcontrol' => array(
            array(
                'label' => 'Flights',
                'route' => 'home',
                'pages' => array(
                    array(
                        'label' => 'Active',
                        'route' => 'flightsActive',
                    ),
                    array(
                        'label' => 'Archive',
                        'route' => 'flightsArchived',
                    ),
                ),
            ),
            array(
                'label' => 'Libraries',
                'route' => 'zfcadmin/libraries',
                'pages' => array(
                    array(
                        'label' => 'Aircrafts',
                        'route' => 'zfcadmin/aircrafts',
                    ),
                    array(
                        'label' => 'Types Aircraft',
                        'route' => 'zfcadmin/aircraft_types',
                    ),
                    array(
                        'label' => 'Air Operators',
                        'route' => 'zfcadmin/air_operators',
                    ),
                    array(
                        'label' => 'Airports',
                        'route' => 'zfcadmin/airports',
                    ),
                    array(
                        'label' => 'Base of Permits',
                        'route' => 'zfcadmin/base_of_permits',
                    ),
                    array(
                        'label' => 'Cities',
                        'route' => 'zfcadmin/cities',
                    ),
                    array(
                        'label' => 'Countries',
                        'route' => 'zfcadmin/countries',
                    ),
                    array(
                        'label' => 'Currencies',
                        'route' => 'zfcadmin/currencies',
                    ),
                    array(
                        'label' => 'Kontragents',
                        'route' => 'zfcadmin/kontragents',
                    ),
                    array(
                        'label' => 'Regions',
                        'route' => 'zfcadmin/regions',
                    ),
                    array(
                        'label' => 'Units',
                        'route' => 'zfcadmin/units',
                    ),

                    array(
                        'type' => 'uri',
                        'divider' => true,
                    ),
                    array(
                        'label' => 'Advanced search',
                        'route' => 'zfcadmin/advanced_search',
                    ),
                ),
            ),
            array(
                'label' => 'Logs',
                'route' => 'logs',
                'action' => 'index',
            ),
            array(
                'label' => 'Users',
                'route' => 'zfcadmin/users',
                'action' => 'index',
            ),
        ),
    ),
);
