<?php

namespace Zenstruck\DataGridBundle\Pager\Pagerfanta;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Zenstruck\DataGridBundle\Executor\DoctrineORMExecutor;
use Zenstruck\DataGridBundle\Executor\ExecutorInterface;
use Zenstruck\DataGridBundle\Field\FieldCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DoctrineORMPagerfantaPager extends PagerfantaPager
{
    protected function createAdapter(FieldCollection $fields, ExecutorInterface $executor)
    {
        if (!$executor instanceof DoctrineORMExecutor) {
            throw new \InvalidArgumentException('Executor must be an instance of DoctrineORMExecutor');
        }

        return new DoctrineORMAdapter($executor->filterQuery($fields));
    }
}
