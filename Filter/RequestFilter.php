<?php

namespace Zenstruck\DataGridBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Routing\RouterInterface;
use Zenstruck\DataGridBundle\Field\Field;
use Zenstruck\DataGridBundle\Field\FieldCollection;
use Zenstruck\DataGridBundle\Pager\PagerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RequestFilter implements PagerFilterInterface
{
    const PARAM_FILTER       = 'filter';
    const PARAM_SORT         = 'sort';
    const PARAM_PAGE         = 'page';
    const PARAM_MAX_PER_PAGE = 'max_per_page';

    protected $request;
    protected $router;
    protected $filterParam;
    protected $sortParam;
    protected $route;
    protected $routeParams;

    public function __construct(Request $request, RouterInterface $router, $filterParam = self::PARAM_FILTER, $sortParam = self::PARAM_SORT)
    {
        $this->request = $request;
        $this->router = $router;
        $this->filterParam = $filterParam;
        $this->sortParam = $sortParam;
        $this->route = $this->request->get('_route');
        $this->routeParams = array_merge($this->request->query->all(), $this->request->attributes->get('_route_params', array()));
    }

    /**
     * @param FieldCollection $fieldCollection
     * @return FieldCollection
     */
    public function filter(FieldCollection $fieldCollection)
    {
        $filters = $this->request->query->get($this->filterParam, array());
        $sorts = $this->request->query->get($this->sortParam, array());

        if (count($sorts)) {
            // clear default sorts
            $fieldCollection->clearSorts();
        }

        return $fieldCollection
            ->setFilterValues($filters)
            ->setSortDirections($sorts)
        ;
    }

    public function generateSortUri($field, $direction)
    {
        $routeParams = $this->routeParams;

        if ($this->isSorted()) {
            unset($routeParams[$this->sortParam]);
        }

        if ($field instanceof Field) {
            $field = $field->getName();
        }

        $propertyPath = new PropertyPath(sprintf('[%s][%s]', $this->sortParam, $field));
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($routeParams, $propertyPath, $direction);

        return $this->router->generate($this->route, $routeParams);
    }

    public function generateFilterUri($field, $value)
    {
        $routeParams = $this->routeParams;

        if ($field instanceof Field) {
            $field = $field->getName();
        }

        $propertyPath = new PropertyPath(sprintf('[%s][%s]', $this->filterParam, $field));
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($routeParams, $propertyPath, (string) $value);

        return $this->router->generate($this->route, $routeParams);
    }

    public function generateResetUri()
    {
        $routeParams = $this->routeParams;

        unset($routeParams[$this->sortParam]);
        unset($routeParams[$this->filterParam]);

        return $this->router->generate($this->route, $routeParams);
    }

    public function getFilterValue($name)
    {
        if (isset($this->routeParams[$this->filterParam][$name])) {
            return $this->routeParams[$this->filterParam][$name];
        }

        return null;
    }

    public function isSorted()
    {
        return isset($this->routeParams[$this->sortParam]);
    }

    public function isFiltered()
    {
        return isset($this->routeParams[$this->filterParam]);
    }

    public function getCurrentPage()
    {
        return $this->request->get('page', 1);
    }

    public function getMaxPerPage()
    {
        return $this->request->get('max_per_page', PagerInterface::DEFAULT_MAX_PER_PAGE);
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getRouteParams()
    {
        return $this->routeParams;
    }
}