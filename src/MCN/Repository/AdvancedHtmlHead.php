<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Repository;
use MCNCore\Object\QueryInfo,
    MCNCore\Object\Entity\Repository,
    MCN\Service\AdvancedHtmlHead as AdvancedHtmlHeadService;

class AdvancedHtmlHead extends Repository
{
    protected $defaultOptions = array(
        AdvancedHtmlHeadService::QUERY_OPT_WITH_NULL_RAW_STRING    => false,
        AdvancedHtmlHeadService::QUERY_OPT_WITHOUT_NULL_RAW_STRING => false,
    );

    protected function getBaseQuery(QueryInfo $qi)
    {
        $dqb = parent::getBaseQuery($qi);

        $options = array_merge($this->defaultOptions, $qi->getQueryOptions());

        if ($options[AdvancedHtmlHeadService::QUERY_OPT_WITHOUT_NULL_RAW_STRING]) {

            $dqb->andWhere('advancedhtmlhead.raw_string is not null');
        }

        if ($options[AdvancedHtmlHeadService::QUERY_OPT_WITH_NULL_RAW_STRING]) {

            $dqb->andWhere('advancedhtmlhead.raw_string is null');
        }

        return $dqb;
    }
}