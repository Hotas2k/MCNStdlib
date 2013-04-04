<?php
/**
 * @author Antoine Hedgecock
 */

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'http'          => 'MCNStdlib\Controller\Plugin\Http',
            'message'       => 'MCNStdlib\Controller\Plugin\Message',
        )
    ),

    'view_helpers' => array(
        'invokables' => array(
            'slugUrl'    => 'MCNStdlib\View\Helper\SlugUrl',
            'pagination' => 'MCNStdlib\View\Helper\Pagination',
            'stringTrim' => 'MCNStdlib\View\Helper\StringTrim',
            'sm'         => 'MCNStdlib\View\Helper\ServiceManager',
        )
    )
);
