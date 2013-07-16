<?php

namespace Zenstruck\DataGridBundle\Filter;

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
}