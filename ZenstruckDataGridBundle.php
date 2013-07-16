<?php

namespace Zenstruck\DataGridBundle;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zenstruck\DataGridBundle\DependencyInjection\ZenstruckDataGridExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckDataGridBundle extends Bundle
{
    public function getContainerExtension()
    {
        return $this->extension = new ZenstruckDataGridExtension();
    }
}