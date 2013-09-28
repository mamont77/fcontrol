<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
return array(
    'dblog' => array(
        'tableName' => 'logs',
        'columnMap' => array(
            'timestamp' => 'timestamp',
            'priority' => 'priority',
            'priorityName' => 'priorityName',
            'message' => 'message',
            'extra' => array(
                'url' => 'url',
                'ipaddress' => 'ipaddress',
                'username' => 'username',
                'component' => 'component',
            ),
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'DBLog\Controller\DBLog' => 'DBLog\Controller\DBLogController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);
?>
