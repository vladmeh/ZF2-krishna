<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Users\Controller\Index' => 'Users\Controller\IndexController',
            'Users\Controller\Register' => 'Users\Controller\RegisterController',
            'Users\Controller\Login' => 'Users\Controller\LoginController',
            'Users\Controller\UserManager' => 'Users\Controller\UserManagerController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'users' => __DIR__ . '/../view',
        ),
    ),

    'router' => array(
        'routes' => array(
            'users' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/users',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Users\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type'    => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route'    => '/login[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\Login',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type'    => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route'    => '/register[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\Register',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'user-manager' => array(
                        'type'    => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route'    => '/user-manager[/:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\UserManager',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);