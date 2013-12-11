<?php

namespace Zenstruck\DataGridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExportTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('zenstruck_datagrid.export_service')) {
            return;
        }

        $definition = $container->getDefinition(
            'zenstruck_datagrid.export_service'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'zenstruck_datagrid.export_type'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addExportType', array(new Reference($id)));
        }
    }
}
