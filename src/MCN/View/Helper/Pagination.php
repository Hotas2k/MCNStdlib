<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\View\Helper;
use Zend\View\Helper\AbstractHelper,
    MCN\Pagination\Pagination as PaginationContainer;

class Pagination extends AbstractHelper
{
    public function __invoke($pagination, $templateKey = 'pagination')
    {
        // check if pagination is required
        if ($pagination instanceof PaginationContainer && $pagination->isRequired()) {

            return $this->getView()
                        ->partial($templateKey, array('pagination' => $pagination));
        }
    }
}
