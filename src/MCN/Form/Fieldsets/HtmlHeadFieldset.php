<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Form\Fieldsets;
use Zend\Form\Fieldset,
    Zend\Stdlib\Hydrator\ClassMethods,
    Zend\InputFilter\InputFilterProviderInterface;

class HtmlHeadFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('html-head_fieldset');

        $this->setHydrator(new ClassMethods())
             ->setObject(new \MCN\Entity\HtmlHead\Page());

        $this->add(array(
            'name'       => 'name',
            'attributes' => array(

                'disabled' => 'disabled'
            )
        ));

        $this->add(array(
            'name' => 'title',
        ));

        $this->add(array(
            'name' => 'description',
        ));

        $this->add(array(
            'name'       => 'keywords',
            'attributes' => array(

                'class' => 'droppable'
            )
       ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(

            'title' => array(

                'required'    => true,
                'allow_empty' => false,
            ),

            'description' => array(

                'required'    => true,
                'allow_empty' => true
            ),

            'keywords' => array(

                'required'    => true,
                'allow_empty' => true
            )
        );
    }
}