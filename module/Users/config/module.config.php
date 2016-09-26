<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Users\Controller\Index' => 'Users\Controller\IndexController',
            'Users\Controller\Register' => 'Users\Controller\RegisterController',
            'Users\Controller\Login' => 'Users\Controller\LoginController',
            'Users\Controller\UserManager' => 'Users\Controller\UserManagerController',
            'Users\Controller\UploadManager' => 'Users\Controller\UploadManagerController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'users' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/default-layout.phtml',
            'layout/myaccount'           => __DIR__ . '/../view/layout/myaccount-layout.phtml',
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
                    'upload-manager' => array(
                        'type'    => 'Segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route'    => '/upload-manager[/:action[/:id]]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Users\Controller\UploadManager',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'module_config' => array(
        'upload_location' => __DIR__ . '/../data/uploads',
    ),

    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'users',
            ),
            array(
                'label' => 'Login',
                'route' => 'users/login',
            ),
            array(
                'label' => 'Register',
                'route' => 'users/register',
            ),
        ),
        'account' => array(
            array(
                'label' => 'Home',
                'route' => 'users',
            ),
            array(
                'label' => 'Manage Users',
                'route' => 'users/user-manager',
            ),
            array(
                'label' => 'Manage Documents',
                'route' => 'users/upload-manager',
            ),
            array(
                'label' => 'Logout',
                'route' => 'users/login',
                'action' => 'logout'
            ),
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'account' => 'Users\Navigation\Service\AccountNavigationFactory',
        ),
    ),
);