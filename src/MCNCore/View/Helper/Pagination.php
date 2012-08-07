<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\View\Helper;
use Zend\View\Helper\AbstractHelper,
    MCNCore\Pagination\Pagination as PaginationContainer;

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