<?php

namespace Zenstruck\DataGridBundle;

use Zenstruck\DataGridBundle\Field\FieldCollection;
use Zenstruck\DataGridBundle\Filter\FilterInterface;
use Zenstruck\DataGridBundle\Executor\ExecutorInterface;
use Zenstruck\DataGridBundle\Filter\PagerFilterInterface;
use Zenstruck\DataGridBundle\Pager\PagerInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class PaginatedGrid extends Grid
{
    protected $currentPage;
    protected $maxPerPage;
    protected $totalPages;
    protected $totalResults;

    /**
     * @Serializer\Exclude
     *
     * @var PagerInterface
     */
    protected $pager;

    public function __construct(
        $name,
        FieldCollection $fields,
        FilterInterface $filter,
        ExecutorInterface $executor,
        PagerInterface $pager
    ) {
        parent::__construct($name, $fields, $filter, $executor);

        $this->pager = $pager;
    }

    /**
     * @return PaginatedGrid
     */
    public function execute()
    {
        if ($this->executed) {
            return $this;
        }

        $this->filter->filter($this->fields);

        if ($this->filter instanceof PagerFilterInterface) {
            $this->pager->execute($this->fields, $this->executor, $this->filter->getCurrentPage(), $this->filter->getMaxPerPage());
        } else {
            $this->pager->execute($this->fields, $this->executor);
        }

        $this->results = $this->pager->getResults();

        if ($this->results instanceof \Traversable) {
            $this->results = iterator_to_array($this->results);
        }

        $this->currentPage = $this->pager->getCurrentPage();
        $this->maxPerPage = $this->pager->getMaxPerPage();
        $this->totalPages = $this->pager->getTotalPages();
        $this->totalResults = $this->pager->getTotalResults();
        $this->executed = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        $this->ensureExecuted();

        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        $this->ensureExecuted();

        return $this->maxPerPage;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        $this->ensureExecuted();

        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getTotalResults()
    {
        $this->ensureExecuted();

        return $this->totalResults;
    }

    /**
     * @return \Zenstruck\DataGridBundle\Pager\PagerInterface
     */
    public function getPager()
    {
        $this->ensureExecuted();

        return $this->pager;
    }
}