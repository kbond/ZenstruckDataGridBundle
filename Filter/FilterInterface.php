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
}