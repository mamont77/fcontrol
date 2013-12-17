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
                'label' => 'Management',
                'route' => 'management',
                'pages' => array(
                    array(
                        'label' => 'Refuel income',
                        'route' => 'management/refuel/income-invoice-step1',
                    ),
                    array(
                        'label' => 'Refuel outcome',
                        'route' => 'management/refuel/outcome-invoice-step1',
                    ),
                    array(
                        'label' => 'AP Service income',
                        'route' => 'management/ap-service/income-invoice-step1',
                    ),
                    array(
                        'label' => 'AP Service outcome',
                        'route' => 'management/ap-service/outcome-invoice-step1',
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
                        'label' => 'Type of AP Services',
                        'route' => 'zfcadmin/type_of_ap_services',
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
