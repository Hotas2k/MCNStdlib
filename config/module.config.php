<?php
/**
 * @author Antoine Hedgecock
 */
return array(
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
        )
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'http'          => 'MCN\Controller\Plugin\Http',
            'message'       => 'MCN\Controller\Plugin\Message',
            'searchStorage' => 'MCN\Controller\Plugin\SearchStorage',
        )
    ),

    'service_manager' => array(

        'invokables' => array(

            'mcn.doctrine_logger' => 'Doctrine\DBAL\Logging\DebugStack'
        ),

        'factories' => array(
            'mcn.memcached'     => 'MCN\Factory\MemcachedFactory',
            'mcn.sphinx_client' => 'MCN\Factory\SphinxClientFactory'
        )
    ),

    'view_helpers' => array(
        'invokables' => array(
            'slugUrl'    => 'MCN\View\Helper\SlugUrl',
            'pagination' => 'MCN\View\Helper\Pagination',
            'stringTrim' => 'MCN\View\Helper\StringTrim',
            'sm'         => 'MCN\View\Helper\ServiceManager',
        )
    )
);
