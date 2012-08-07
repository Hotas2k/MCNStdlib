<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCNCore\Object;
use Zend\Mvc\MvcEvent,
    Doctrine\DBAL\Logging\DebugStack,
    BjyProfiler\Db\Profiler\Profiler as BjyProfiler,
    ZendDeveloperTools\Collector\AutoHideInterface,
    ZendDeveloperTools\Collector\CollectorInterface;

class Profiler implements CollectorInterface, AutoHideInterface
{
    public function __construct(DebugStack $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns true if the collector can be hidden, because it is empty.
     *
     * @return boolean
     */
    public function canHide()
    {
        return empty($this->logger->queries);
    }

    /**
     * Collector Name.
     *
     * @return string
     */
    public function getName()
    {
        return 'db';
    }

    /**
     * Collector Priority.
     *
     * @return integer
     */
    public function getPriority()
    {
        return 10;
    }

    /**
     * Collects data.
     *
     * @param MvcEvent $mvcEvent
     */
    public function collect(MvcEvent $mvcEvent)
    {
        // Must implement don't know why (:
    }

    public function hasProfiler()
    {
        return true;
    }

    public function getQueryCount($mode = null)
    {
        $count = 0;
        $qType = null;

        switch($mode)
        {
            case BjyProfiler::INSERT:
                $qType = 'insert';
                break;

            case BjyProfiler::UPDATE:
                $qType = 'update';
                break;

            case BjyProfiler::SELECT:
                $qType = 'select';
                break;

            case BjyProfiler::DELETE:
                $qType = 'delete';
                break;
        }

        foreach($this->logger->queries as $q)
        {
            $exp = explode(' ', $q['sql'], 2);

            if ($qType === null || $qType == strtolower($exp[0])) {

                $count++;
            }
        }

        return $count;
    }

    public function getQueryTime($mode = null)
    {
        $time  = 0.0;
        $qType = null;

        switch($mode)
        {
            case BjyProfiler::INSERT:
                $qType = 'insert';
                break;

            case BjyProfiler::UPDATE:
                $qType = 'update';
                break;

            case BjyProfiler::SELECT:
                $qType = 'select';
                break;

            case BjyProfiler::DELETE:
                $qType = 'delete';
                break;
        }

        foreach($this->logger->queries as $q)
        {
            $exp = explode(' ', $q['sql'], 2);

            if ($qType === null || $qType == strtolower($exp[0])) {

                $time += $q['executionMS'];
            }
        }

        return $time;
    }
}