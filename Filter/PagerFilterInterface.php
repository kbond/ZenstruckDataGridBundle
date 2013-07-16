<?php

namespace Zenstruck\DataGridBundle\Filter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface PagerFilterInterface extends FilterInterface
{
    /**
     * @return int
     */
    public function getCurrentPage();

    /**
     * @return int
     */
    public function getMaxPerPage();
}