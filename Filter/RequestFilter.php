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
    const PARAM_QUERY        = 'query';
    const PARAM_MAX_PER_PAGE = 'max_per_page';

    protected $request;
    protected $router;
    protected $route;
    protected $routeParams;

    public function __construct(Request $request, RouterInterface $router)
    {
        $this->request = $request;
        $this->router = $router;
        $this->route = $this->request->get('_route');
        $this->routeParams = array_merge($this->request->query->all(), $this->request->attributes->get('_route_params', array()));

        if (!isset($this->routeParams[static::PARAM_SORT])) {
            $this->routeParams[static::PARAM_SORT] = array();
        }

        if (!isset($this->routeParams[static::PARAM_FILTER])) {
            $this->routeParams[static::PARAM_FILTER] = array();
        }

        if (!isset($this->routeParams[static::PARAM_QUERY])) {
            $this->routeParams[static::PARAM_QUERY] = null;
        }
    }

    /**
     * @param  FieldCollection $fieldCollection
     * @return FieldCollection
     */
    public function filter(FieldCollection $fieldCollection)
    {
        if (count($this->routeParams[static::PARAM_SORT])) {
            // clear default sorts
            $fieldCollection->clearSorts();
        }

        $fieldCollection = $fieldCollection
            ->setFilterValues($this->routeParams[static::PARAM_FILTER])
            ->setSortDirections($this->routeParams[static::PARAM_SORT])
            ->setSearchQuery($this->routeParams[static::PARAM_QUERY])
        ;

        // normalize filter params
        $this->routeParams[static::PARAM_FILTER] = $fieldCollection->buildFilterArray();

        return $fieldCollection;
    }

    public function getUri()
    {
        return $this->router->generate($this->route, $this->routeParams);
    }

    public function getSearchQuery()
    {
        return $this->routeParams[static::PARAM_QUERY];
    }

    public function generateSortUri($field, $direction)
    {
        $routeParams = $this->routeParams;

        // remove all sorts first
        if ($this->isSorted()) {
            unset($routeParams[static::PARAM_SORT]);
        }

        if ($field instanceof Field) {
            $field = $field->getName();
        }

        $propertyPath = new PropertyPath(sprintf('[%s][%s]', static::PARAM_SORT, $field));
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

        $propertyPath = new PropertyPath(sprintf('[%s][%s]', static::PARAM_FILTER, $field));
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($routeParams, $propertyPath, $value);

        return $this->router->generate($this->route, $routeParams);
    }

    public function generateResetUri()
    {
        $routeParams = $this->routeParams;

        unset($routeParams[static::PARAM_QUERY]);
        unset($routeParams[static::PARAM_SORT]);
        unset($routeParams[static::PARAM_FILTER]);

        return $this->router->generate($this->route, $routeParams);
    }

    public function generateSearchQueryUri($query)
    {
        $routeParams = $this->routeParams;

        $propertyPath = new PropertyPath(sprintf('[%s]', static::PARAM_QUERY));
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($routeParams, $propertyPath, $query);

        return $this->router->generate($this->route, $routeParams);
    }

    public function getFilterValue($name)
    {
        if (isset($this->routeParams[static::PARAM_FILTER][$name])) {
            return $this->routeParams[static::PARAM_FILTER][$name];
        }

        return null;
    }

    public function getFilters()
    {
        return $this->routeParams[static::PARAM_FILTER];
    }

    public function getSorts()
    {
        return $this->routeParams[static::PARAM_SORT];
    }

    public function isSorted()
    {
        return (bool) count($this->routeParams[static::PARAM_SORT]);
    }

    public function isFiltered()
    {
        return (bool) count($this->routeParams[static::PARAM_FILTER]);
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
