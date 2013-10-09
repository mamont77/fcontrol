<?php

return array(
    'bjyauthorize' => array(

        // set the 'guest' role as default (must be defined in a role provider)
        'default_role' => 'guest',

        /* this module uses a meta-role that inherits from any roles that should
         * be applied to the active user. the identity provider tells us which
         * roles the "identity role" should inherit from.
         *
         * for ZfcUser, this will be your default identity provider
         */
        'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',

        /* role providers simply provide a list of roles that should be inserted
         * into the Zend\Acl instance. the module comes with two providers, one
         * to specify roles in a config file and one to load roles using a
         * Zend\Db adapter.
         */
        'role_providers' => array(

            /* here, 'guest' and 'user are defined as top-level roles, with
             * 'admin' inheriting from user
             */
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array(),
                'user' => array('children' => array(
                    'admin' => array(),
                )),
            ),

            // this will load roles from the user_role table in a database
            // format: user_role(role_id(varchar), parent(varchar))
            'BjyAuthorize\Provider\Role\ZendDb' => array(
                'table' => 'user_role',
                'role_id_field' => 'roleId',
                'parent_role_field' => 'parent_id',
            ),

            // this will load roles from
            // the 'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' service
//            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
//                // class name of the entity representing the role
//                'role_entity_class' => 'My\Role\Entity',
//                // service name of the object manager
//                'object_manager'    => 'My\Doctrine\Common\Persistence\ObjectManager',
//            ),
        ),

        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'admin' => array(),
                //'pants' => array(),
            ),
        ),

        /* rules can be specified here with the format:
         * array(roles (array), resource, [privilege (array|string), assertion])
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    // allow guests and users (and admins, through inheritance)
                    // the "wear" privilege on the resource "pants"
                    //array(array('guest', 'user'), 'pants', 'wear'),
                    array(array('admin'), 'admin'),
                ),

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array( // ...
                ),
            ),
        ),

        /* Currently, only controller and route guards exist
         */
        'guards' => array(
            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all controllers and actions unless they are specified here.
             * You may omit the 'action' index to allow access to the entire controller
             */
            'BjyAuthorize\Guard\Controller' => array(
                //array('controller' => 'index', 'action' => 'index', 'roles' => array('guest','user')),
                //array('controller' => 'zfcuseradmin', 'roles' => array('admin')),
                array('controller' => 'zfcuser', 'roles' => array()),
                array('controller' => 'Application\Controller\Index', 'roles' => array('guest', 'user')),
                array('controller' => 'FcAdmin\Controller\Index', 'roles' => array('admin')),
                array('controller' => 'FcAdmin\Controller\User', 'roles' => array('admin')),
                //array('controller' => 'ZfcUserAdmin\Controller\UserAdmin', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Aircraft', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\AircraftType', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\AirOperator', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Airport', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\BaseOfPermit', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\City', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Country', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Currency', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Index', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Kontragent', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Region', 'roles' => array('admin')),
                array('controller' => 'FcLibraries\Controller\Unit', 'roles' => array('admin')),
                array('controller' => 'FcLibrariesSearch\Controller\Search', 'roles' => array('admin')),
                array('controller' => 'FcFlight\Controller\Flight', 'roles' => array('user')),
                array('controller' => 'FcFlight\Controller\Search', 'roles' => array('user')),
                array('controller' => 'FcFlight\Controller\Leg', 'roles' => array('user')),
                array('controller' => 'FcFlight\Controller\Permission', 'roles' => array('user')),
                array('controller' => 'FcFlight\Controller\Refuel', 'roles' => array('user')),
                array('controller' => 'FcLogEvents\Controller\Index', 'roles' => array('user')),
                array('controller' => 'FcLogEvents\Controller\Search', 'roles' => array('user')),
            ),

            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'home', 'roles' => array('guest', 'user')),

                array('route' => 'zfcuser', 'roles' => array('user')),
                array('route' => 'zfcuser/logout', 'roles' => array('user')),
                array('route' => 'zfcuser/login', 'roles' => array('guest')),
                array('route' => 'zfcuser/register', 'roles' => array('guest')),
                array('route' => 'zfcuser/changepassword', 'roles' => array('user')),
                array('route' => 'zfcuser/changeemail', 'roles' => array('user')),

                array('route' => 'flightsActive', 'roles' => array('user')),
                array('route' => 'flightsArchived', 'roles' => array('user')),
                array('route' => 'flightsSearch', 'roles' => array('user')),
                array('route' => 'flight', 'roles' => array('user')),
                array('route' => 'browse', 'roles' => array('user')),

                array('route' => 'leg', 'roles' => array('user')),
                array('route' => 'permission', 'roles' => array('user')),
                array('route' => 'refuel', 'roles' => array('user')),

                array('route' => 'zfcadmin', 'roles' => array('admin')),
                array('route' => 'zfcadmin/dashboard', 'roles' => array('admin')),
                array('route' => 'zfcadmin/users', 'roles' => array('admin')),
                array('route' => 'zfcadmin/user', 'roles' => array('admin')),

                array('route' => 'zfcadmin/libraries', 'roles' => array('admin')),
                array('route' => 'zfcadmin/aircrafts', 'roles' => array('admin')),
                array('route' => 'zfcadmin/aircraft', 'roles' => array('admin')),
                array('route' => 'zfcadmin/aircraft_types', 'roles' => array('admin')),
                array('route' => 'zfcadmin/aircraft_type', 'roles' => array('admin')),
                array('route' => 'zfcadmin/air_operators', 'roles' => array('admin')),
                array('route' => 'zfcadmin/air_operator', 'roles' => array('admin')),
                array('route' => 'zfcadmin/airports', 'roles' => array('admin')),
                array('route' => 'zfcadmin/airport', 'roles' => array('admin')),
                array('route' => 'zfcadmin/base_of_permits', 'roles' => array('admin')),
                array('route' => 'zfcadmin/base_of_permit', 'roles' => array('admin')),
                array('route' => 'zfcadmin/cities', 'roles' => array('admin')),
                array('route' => 'zfcadmin/city', 'roles' => array('admin')),
                array('route' => 'zfcadmin/countries', 'roles' => array('admin')),
                array('route' => 'zfcadmin/country', 'roles' => array('admin')),
                array('route' => 'zfcadmin/currencies', 'roles' => array('admin')),
                array('route' => 'zfcadmin/currency', 'roles' => array('admin')),
                array('route' => 'zfcadmin/kontragents', 'roles' => array('admin')),
                array('route' => 'zfcadmin/kontragent', 'roles' => array('admin')),
                array('route' => 'zfcadmin/regions', 'roles' => array('admin')),
                array('route' => 'zfcadmin/region', 'roles' => array('admin')),
                array('route' => 'zfcadmin/units', 'roles' => array('admin')),
                array('route' => 'zfcadmin/unit', 'roles' => array('admin')),
                array('route' => 'zfcadmin/advanced_search', 'roles' => array('admin')),
                array('route' => 'logs', 'roles' => array('user')),
                array('route' => 'logsSearch', 'roles' => array('user')),
            ),
        ),

        // Template name for the unauthorized strategy
        'template'              => 'error/403.phtml',
    ),

);
