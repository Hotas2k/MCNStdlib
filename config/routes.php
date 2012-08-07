<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */
return array(
    'html-head' => array(

        'type'    => 'literal',
        'options' => array(

            'route'    => '/html-head',
        ),

        'may_terminate' => false,
        'child_routes'  => array(

            'category' => array(

                'type'    => 'literal',
                'options' => array(

                    'route' => '/category',
                ),

                'may_terminate' => false,
                'child_routes'  => array(

                    'list' => array(

                        'type'    => 'literal',
                        'options' => array(

                            'route'    => '/list',
                            'defaults' => array(

                                'controller' => 'html-head_category',
                                'action'     => 'list'
                            )
                        )
                    ),

                    'edit' => array(

                        'type' => 'segment',
                        'options' => array(

                            'route'    => '/edit/:id',
                            'defaults' => array(

                                'controller' => 'html-head_category',
                                'action'     => 'edit'
                            )
                        )
                    ),
                    'preview' => array(

                        'type' => 'segment',
                        'options' => array(

                            'route'    => '/preview/:id',
                            'defaults' => array(

                                'controller' => 'html-head_category',
                                'action'     => 'preview'
                            )
                        )
                    )
                )
            ),

            'page' => array(

                'type'    => 'literal',
                'options' => array(

                    'route' => '/page',
                ),

                'may_terminate' => false,
                'child_routes'  => array(

                    'list' => array(

                        'type'    => 'literal',
                        'options' => array(

                            'route'    => '/list',
                            'defaults' => array(

                                'controller' => 'html-head_page',
                                'action'     => 'list'
                            )
                        )
                    ),

                    'edit' => array(

                        'type' => 'segment',
                        'options' => array(

                            'route'    => '/edit/:id',
                            'defaults' => array(

                                'controller' => 'html-head_page',
                                'action'     => 'edit'
                            )
                        )
                    ),

                    'preview' => array(

                        'type' => 'segment',
                        'options' => array(

                            'route'    => '/preview/:id',
                            'defaults' => array(

                                'controller' => 'html-head_page',
                                'action'     => 'preview'
                            )
                        )
                    )
                )
            )
        )
    )
);