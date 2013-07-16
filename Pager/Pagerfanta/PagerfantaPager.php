<?php

namespace Zenstruck\DataGridBundle\Pager\Pagerfanta;

use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Zenstruck\DataGridBundle\Executor\ExecutorInterface;
use Zenstruck\DataGridBundle\Field\FieldCollection;
use Zenstruck\DataGridBundle\Pager\PagerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class PagerfantaPager implements PagerInterface
{
    /** @var Pagerfanta */
    protected $pagerfanta;

    public function getCurrentPage()
    {
        return $this->pagerfanta->getCurrentPage();
    }

    public function getTotalPages()
    {
        return $this->pagerfanta->getNbPages();
    }

    public function getTotalResults()
    {
        return $this->pagerfanta->getNbResults();
    }

    public function getMaxPerPage()
    {
        return $this->pagerfanta->getMaxPerPage();
    }

    public function getResults()
    {
        return $this->pagerfanta->getCurrentPageResults();
    }

    public function execute(FieldCollection $fields, ExecutorInterface $executor, $currentPage = 1, $maxPerPage = self::DEFAULT_MAX_PER_PAGE)
    {
        // normalize page number
        if (!is_numeric($currentPage) || $currentPage < 1) {
            $currentPage = 1;
        }

        // normalize max per page
        if (!is_numeric($maxPerPage) || $maxPerPage < 1) {
            $maxPerPage = 1;
        }

        $pagerfanta = new Pagerfanta($this->createAdapter($fields, $executor));
        $pagerfanta->setNormalizeOutOfRangePages(true);

        // run query
        $pagerfanta->getNbResults();
        $pagerfanta->setMaxPerPage($maxPerPage);

        // must be set after query and after max page is set to get proper page count
        $pagerfanta->setCurrentPage($currentPage);

        $this->pagerfanta = $pagerfanta;
    }

    public function getPaginator()
    {
        return $this->pagerfanta;
    }

    /**
     * @param FieldCollection $fields
     * @param ExecutorInterface $executor
     *
     * @return AdapterInterface
     */
    abstract protected function createAdapter(FieldCollection $fields, ExecutorInterface $executor);
}