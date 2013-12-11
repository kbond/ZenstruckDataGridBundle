<?php

namespace Zenstruck\DataGridBundle\Executor;

use Zenstruck\DataGridBundle\Field\FieldCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface ExecutorInterface
{
    /**
     * @param FieldCollection $fieldCollection
     *
     * @return array
     */
    public function execute(FieldCollection $fieldCollection);
}
