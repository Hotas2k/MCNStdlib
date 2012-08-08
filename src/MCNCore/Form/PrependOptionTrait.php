<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Form;

trait PrependOptionTrait
{
    protected function prependOption($form, $elementName, array $option)
    {
        foreach(explode('.', $elementName) as $part)
        {
            $form = $form->get($part);
        }

        $options = $form->getAttribute('options');

        $form->setAttribute('options', $option + $options);
    }
}