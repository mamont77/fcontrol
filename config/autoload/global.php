<?php

$dbParams = array(
    'database' => 'zf2tutorial',
    'username' => 'root',
    'password' => '',
    'hostname' => 'localhost',
    // buffer_results - only for mysqli buffered queries, skip for others
    'options' => array('buffer_results' => true)
);

$navigation = array(
    'admin' => array(
        'home' => array(
            'label' => 'fControl (alfa)',
            'route' => 'home',
            'class' => 'brand',
        ),
        'dashboard' => array(
            'label' => 'Панель управления',
            'route' => 'zfcadmin',
            'liClass' => 'test',
            'pages' => array(
                'user' => array(
                    'label' => 'Управление пользователями',
                    'route' => 'zfcadmin/users',
                ),
                array(
                    'label' => 'Библиотека',
                    'route' => 'zfcadmin/libraries',
                    'class' => 'dropdown-toggle ',
                    'pages' => array(
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
                    )
                ),
            ),
        ),
    )
);

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($sm) use ($dbParams) {
                $adapter = new BjyProfiler\Db\Adapter\ProfilingAdapter(array(
                    'driver' => 'pdo',
                    'dsn' => 'mysql:dbname=' . $dbParams['database'] . ';host=' . $dbParams['hostname'],
                    'database' => $dbParams['database'],
                    'username' => $dbParams['username'],
                    'password' => $dbParams['password'],
                    'hostname' => $dbParams['hostname'],
                ));

                $adapter->setProfiler(new BjyProfiler\Db\Profiler\Profiler);
                if (isset($dbParams['options']) && is_array($dbParams['options'])) {
                    $options = $dbParams['options'];
                } else {
                    $options = array();
                }
                $adapter->injectProfilingStatementPrototype($options);
                return $adapter;
            },
        ),
    ),
    'navigation' => $navigation,
);
