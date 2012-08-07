<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\View\Helper;
use MCNCore\Object\AbstractObject,
    Zend\View\Helper\AbstractHelper,
    Doctrine\Common\Collections\Collection,

    MCN\Entity\HtmlHead\Page  as PageEntity,
    MCN\Service\HtmlHead\Page as PageService;

class DynamicHtmlHead extends AbstractHelper
{
    /**
     * @var \MCN\Service\HtmlHead\Page
     */
    protected $service;

    /**
     * @param \MCN\Service\HtmlHead\Page $service
     */
    public function __construct(PageService $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $name
     * @param string $namespace
     * @param array  $fields
     * @param array  $variables
     * @param bool   $pagination
     *
     * @return mixed
     */
    public function __invoke($name, $namespace, array $variables = array(), $pagination = false)
    {
        // Get the raw string from the database/cache
        $result = $this->service->get($name);

        // if we have a valid result
        if ($result) {

            $this->view->headTitle($this->renderTitle($result, $variables));
            $this->view->headMeta($this->renderKeywords($result, $variables), 'keywords');
            $this->view->headMeta($this->renderDescription($result, $variables), 'description');

        } else {

            // add a new page for meta data in the database
            $this->service->create($name, $namespace, $pagination, $variables);
        }
    }

    public function renderTitle(PageEntity $page, $data)
    {
        return preg_replace_callback('/%([^%]+)%/', function($matches) use ($data) {

            $exp = explode('.', $matches[1]);

            $var = $data;

            foreach($exp as $k) {

                if (isSet($var[$k])) {

                    $var = $var[$k];
                } else {

                    // TODO: Add logging, discuss if necessary
                    return '';
                }
            }

            if (is_string($var)) {

                return $var;
            }

        }, $page->getTitle());
    }

    public function renderDescription(PageEntity $page, $data)
    {
        return preg_replace_callback('/%([^%]+)%/', function($matches) use ($data) {

            $exp = explode('.', $matches[1]);

            $var = $data;

            foreach($exp as $k) {

                if (isSet($var[$k])) {

                    $var = $var[$k];
                } else {

                    // TODO: Add logging, discuss if necessary
                    return '';
                };
            }

            if (is_string($var)) {

                return $var;
            }

        }, $page->getDescription());
    }

    public function renderKeywords(PageEntity $page, $data)
    {
        $keywords = array();

        foreach(explode(',', $page->getKeywords()) as $keyword)
        {
            $keyword = trim($keyword);

            if (substr($keyword, 0, 1) == '%' && substr($keyword, -1, 1) == '%') {

                // Removed % from the string
                $keyword = substr($keyword, 1, -1);

                // Get the value from the data
                $keyword = $this->getVariableFromData($keyword, $data);

                // Key was not found
                if ($keyword == null) continue;

                // Returned object as a collection
                if( $keyword instanceof Collection) {

                    $keyword = $keyword->toArray();
                }

                // Keyword was empty
                if (empty($keyword)) continue;

                // Turn into a string
                if (is_array($keyword)) {

                    $keyword = implode(', ', $keyword);
                }
            }

            // Append
            $keywords[] = $keyword;
        }

        return implode(',', $keywords);
    }

    protected function getVariableFromData($keyword, $data)
    {
        foreach(explode('.', $keyword) as $k) {

            if (isSet($data[$k])) {

                $data = $data[$k];
            } else {

                return null;
            }
        }

        return $data;
    }
}