<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Object\Entity\Behavior;

interface ViewableInterface extends IdentifiableInterface
{
    /**
     * What kind of target we should be tracking
     *
     * @abstract
     * @return string
     */
    public function getViewTargetType();
}
