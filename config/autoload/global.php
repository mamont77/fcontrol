<?php

$dbParams = array(
    'database' => 'fcontrol',
    'username' => 'rapvokza_izstar',
    'password' => 'zenkov77',
    'hostname' => 'localhost',
    // buffer_results - only for mysqli buffered queries, skip for others
    'options' => array('buffer_results' => true)
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
                    'driver_options' => array(
                         PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                    ),
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
            'fcontrol_navigation' => 'Application\Navigation\Service\fControlNavigationFactory',
        ),
    ),
);
