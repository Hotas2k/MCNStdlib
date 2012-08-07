<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Form\Fieldsets;
use Zend\Form\Fieldset,
    Zend\InputFilter\InputProviderInterface;

class DateFieldset extends Fieldset implements InputProviderInterface
{
    public function __construct()
    {
        parent::__construct('company_listing_fieldset');

        $this->add(array(
            'name' => 'year',
        ));

        $this->add(array(
            'name' => 'month'
        ));

        $this->add(array(
            'name' => 'day'
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'year' => array(
                'required'    => true,
                'allow_empty' => false
            ),

            'month' => array(
                'required'    => true,
                'allow_empty' => false
            ),

            'day' => array(
                'required'    => true,
                'allow_empty' => false
            )
        );
    }
}