<?php

namespace Zenstruck\DataGridBundle\Filter;

use Zenstruck\DataGridBundle\Field\Field;
use Zenstruck\DataGridBundle\Field\FieldCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface FilterInterface
{
    /**
     * @param FieldCollection $fieldCollection
     *
     * @return FieldCollection
     */
    public function filter(FieldCollection $fieldCollection);

    /**
     * @param string $name
     *
     * @return string
     */
    public function getFilterValue($name);

    /**
     * @return string
     */
    public function getUri();

    /**
     * @return string
     */
    public function getSearchQuery();

    /**
     * @param Field|string $field
     * @param string       $direction
     *
     * @return string
     */
    public function generateSortUri($field, $direction);

    /**
     * @param Field|string $field
     * @param string       $value
     *
     * @return string
     */
    public function generateFilterUri($field, $value);

    /**
     * @param string $query
     *
     * @return string
     */
    public function generateSearchQueryUri($query);

    /**
     * @return string
     */
    public function generateResetUri();

    /**
     * @return bool
     */
    public function isSorted();

    /**
     * @return bool
     */
    public function isFiltered();

    /**
     * @return array
     */
    public function getFilters();

    /**
     * @return array
     */
    public function getSorts();
}
