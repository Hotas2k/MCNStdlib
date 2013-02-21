<?php
/**
 * @author Antoine Hedgecock
 */

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'http'          => 'MCN\Controller\Plugin\Http',
            'message'       => 'MCN\Controller\Plugin\Message',
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
