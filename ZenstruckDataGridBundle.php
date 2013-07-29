<?php

namespace Zenstruck\DataGridBundle;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zenstruck\DataGridBundle\DependencyInjection\Compiler\ExportTypeCompilerPass;
use Zenstruck\DataGridBundle\DependencyInjection\ZenstruckDataGridExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckDataGridBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ExportTypeCompilerPass());
    }

    public function getContainerExtension()
    {
        return $this->extension = new ZenstruckDataGridExtension();
    }
}