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
     * @param Field  $field
     * @param string $direction
     *
     * @return string
     */
    public function generateSortUri(Field $field, $direction);

    /**
     * @param Field  $field
     * @param string $value
     *
     * @return string
     */
    public function generateFilterUri(Field $field, $value);

    /**
     * @return bool
     */
    public function isSorted();

    /**
     * @return bool
     */
    public function isFiltered();
}