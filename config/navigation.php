<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */
return array(
    'advanced_html_head' => array(

        'type'  => 'mvc',
        'label' => 'HTML Metadata (SEO)',
        'order' => 4,

        'pages' => array(
            array(
                'label' => 'Lista på alla kategorier',
                'route' => 'admin/html-head/category/list'
            ),

            array(
                'label' => 'Lista på alla sidor',
                'route' => 'admin/html-head/page/list'
            )
        )
    )
);