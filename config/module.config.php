<?php
/**
 * @author Antoine Hedgecock
 */
return array(
    'doctrine' => array(
        'driver' => array(
            'mcn_entities' => array(
                'class'     => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache'     => 'array',
                'paths'     => array(
                    'vendor/mcn/mcn/src/MCN/Entity'
                )
            ),

            'orm_default' => array(
                'drivers' => array(
                    'MCN\Entity' => 'mcn_entities'
                )
            )
        )
    ),

    'mcn' => array(

        'logger' => array(

            'mail' => array(

                'to'   => '',
                'from' => ''
            ),

            'log_errors'     => true,
            'log_exceptions' => true
        ),

        'sphinx' => array(

            'host' => 'localhost',
            'port' => 9312
        ),

        'memcached' => array(

            'servers' => array(

                // Supply a default localhost server
                array('127.0.0.1', 11211, 1)
            )
        ),

        'image_library' => array(

            'target_config'  => array()
        )
    ),

    'controllers' => array(
        'invokables' => array(
            'html-head_page'     => 'MCN\Controller\HtmlHead\PageController',
            'html-head_category' => 'MCN\Controller\HtmlHead\CategoryController',
        )
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'user'          => 'MCNCore\Controller\Plugin\User',
            'message'       => 'MCNCore\Controller\Plugin\Message',
            'searchStorage' => 'MCNCore\Controller\Plugin\SearchStorage',
        )
    ),

    'service_manager' => array(

        'invokables' => array(

            'mcn_form_html-head'  => 'MCN\Form\HtmlHead',
            'mcn.doctrine_logger' => 'Doctrine\DBAL\Logging\DebugStack'
        )
    ),

    'view_manager' => array(
        'template_map' => array(
            'mcn/html_head/page/list'    => __DIR__ . '/../view/html_head/page/list.phtml',
            'mcn/html_head/page/edit'    => __DIR__ . '/../view/html_head/page/edit.phtml',
            'mcn/html_head/page/preview' => __DIR__ . '/../view/html_head/page/preview.phtml',

            'mcn/html_head/category/list'    => __DIR__ . '/../view/html_head/category/list.phtml',
            'mcn/html_head/category/edit'    => __DIR__ . '/../view/html_head/category/edit.phtml',
            'mcn/html_head/category/preview' => __DIR__ . '/../view/html_head/category/preview.phtml',

            'mcn/list-available-variables' => __DIR__ . '/../view/list_available_variables.phtml'
        )
    ),

    'view_helpers' => array(
        'invokables' => array(
            'slugUrl'    => 'MCNCore\View\Helper\SlugUrl',
            'pagination' => 'MCNCore\View\Helper\Pagination',
            'stringTrim' => 'MCNCore\View\Helper\StringTrim',
            'sm'         => 'MCNCore\View\Helper\ServiceManager'
        )
    ),

    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => include __DIR__ . '/routes.php'
            )
        )
    ),

    'navigation' => array(

        'admin' => include __DIR__ . '/navigation.php'
    )
);