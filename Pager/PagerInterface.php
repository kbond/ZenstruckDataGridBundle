<?php

namespace Zenstruck\DataGridBundle\Pager;

use Zenstruck\DataGridBundle\Executor\ExecutorInterface;
use Zenstruck\DataGridBundle\Field\FieldCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface PagerInterface
{
    const DEFAULT_MAX_PER_PAGE = 20;

    public function getCurrentPage();

    public function getTotalPages();

    public function getTotalResults();

    public function getResults();

    public function getMaxPerPage();

    public function execute(FieldCollection $fields, ExecutorInterface $executor, $currentPage = 1, $maxPerPage = self::DEFAULT_MAX_PER_PAGE);

    public function getPaginator();
}
