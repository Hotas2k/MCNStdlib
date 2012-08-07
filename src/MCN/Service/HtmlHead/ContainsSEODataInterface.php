<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Service\HtmlHead;

interface ContainsSEODataInterface
{
    /**
     * Retrieves a list of properties that can be used for Search Engine Optimization
     *
     * @abstract
     * @return array
     */
    public function getPropertiesForSEO();
}