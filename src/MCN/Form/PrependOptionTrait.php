<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Form;

trait PrependOptionTrait
{
    protected function prependOption($form, $elementName, array $option)
    {
        foreach(explode('.', $elementName) as $part)
        {
            $form = $form->get($part);
        }

        $options = $form->getAttribute('value_options');



        $form->setAttribute('value_options', $option + $options);
    }
}
