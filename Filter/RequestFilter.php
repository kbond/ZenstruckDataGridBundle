<?php

namespace Zenstruck\DataGridBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Zenstruck\DataGridBundle\Field\FieldCollection;
use Zenstruck\DataGridBundle\Pager\PagerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RequestFilter implements PagerFilterInterface
{
    const PARAM_FILTER = 'filter';
    const PARAM_SORT   = 'sort';
    const PARAM_PAGE   = 'page';
    const PARAM_MAX_PER_PAGE = 'max_per_page';

    protected $request;
    protected $filterParam;
    protected $sortParam;

    public function __construct(Request $request, $filterParam = self::PARAM_FILTER, $sortParam = self::PARAM_SORT)
    {
        $this->request = $request;
        $this->filterParam = $filterParam;
        $this->sortParam = $sortParam;
    }

    /**
     * @param FieldCollection $fieldCollection
     * @return FieldCollection
     */
    public function filter(FieldCollection $fieldCollection)
    {
        $filters = $this->request->query->get($this->filterParam, array());
        $sorts = $this->request->query->get($this->sortParam, array());

        return $fieldCollection
            ->setFilterValues($filters)
            ->setSortDirections($sorts)
        ;
    }

    public function getCurrentPage()
    {
        return $this->request->get('page', 1);
    }

    public function getMaxPerPage()
    {
        return $this->request->get('max_per_page', PagerInterface::DEFAULT_MAX_PER_PAGE);
    }
}