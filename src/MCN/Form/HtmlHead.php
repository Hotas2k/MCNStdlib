<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Form;
use Zend\Form\Form,
    Zend\InputFilter\InputFilter,
    Zend\Stdlib\Hydrator\ClassMethods;

class HtmlHead extends Form
{
    public function __construct()
    {
        parent::__construct('mcn_form_html-head');

        $this->setHydrator(new ClassMethods())
            ->setInputFilter(new InputFilter())
            ->add(
            array(
                 'type'    => 'MCN\Form\Fieldsets\HtmlHeadFieldset',
                 'options' => array(

                     'use_as_base_fieldset' => true
                 )
            )
        );
    }
}