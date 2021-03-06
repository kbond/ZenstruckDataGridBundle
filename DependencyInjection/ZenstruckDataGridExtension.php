<?php

namespace Zenstruck\DataGridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckDataGridExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
        $container->setParameter('zenstruck_datagrid.default_template', $config['default_template']);

        if ($config['export']['enabled']) {
            $container->setParameter('zenstruck_datagrid.export_base_dir', $config['export']['base_dir']);
            $container->setParameter('zenstruck_datagrid.export_global_options', $config['export']['global_options']);
            $loader->load('export/service.xml');

            if ($config['export']['types']['csv']) {
                $loader->load('export/csv.xml');
            }

            if ($config['export']['types']['xls']) {
                $loader->load('export/xls.xml');
            }

            if ($config['export']['types']['xlsx']) {
                $loader->load('export/xlsx.xml');
            }
        }

        $filterDef = $container->getDefinition('zenstruck_datagrid.filter');
        $pagerDef = $container->getDefinition('zenstruck_datagrid.pager');
        $emDef = new Reference('doctrine.orm.default_entity_manager');
        $gridClass = $container->getParameter('zenstruck_datagrid.grid.class');
        $executorClass = $container->getParameter('zenstruck_datagrid.executor.class');
        $paginatedGridClass = $container->getParameter('zenstruck_datagrid.paginated_grid.class');
        $fieldCollectionClass = $container->getParameter('zenstruck_datagrid.field_collection.class');

        foreach ($config['grids'] as $name => $grid) {
            $entity = $grid['entity'];
            $serviceId = $grid['service_id'];

            if (!$serviceId) {
                // build controller id based on bundle and controller name (ie AppBundle:Post becomse app.controller.post)
                preg_match('/^([\w]+)Bundle/', $entity, $matches);
                $serviceId = sprintf('%s.grid.%s', strtolower($matches[1]), $name);
            }

            $fieldsDef = new Definition($fieldCollectionClass, array($grid['fields']));
            $fieldsDef->setPublic(false);

            if ($service = $grid['executor_service']) {
                $executorDef = new Reference($service);
            } else {
                $executorDef = new Definition($executorClass, array($entity, $emDef));
                $executorDef->setPublic(false);
                $container->setDefinition($serviceId.'.executor', $executorDef);
            }

            if ($grid['paginated'] && !$grid['grid_class']) {
                if (!class_exists('Pagerfanta\Pagerfanta')) {
                    throw new InvalidConfigurationException(sprintf('Pagerfanta must be installed to use the paginated feature for grid "%s".', $name));
                }

                $gridClass = $paginatedGridClass;
            } elseif ($grid['grid_class']) {
                $gridClass = $grid['grid_class'];
            }

            $gridDef = new Definition($gridClass, array($name, $fieldsDef, $filterDef, $executorDef));

            $reflectionClass = new \ReflectionClass($gridClass);

            if ($paginatedGridClass === $reflectionClass->getName() || $reflectionClass->isSubclassOf('Zenstruck\DataGridBundle\PaginatedGrid')) {
                $gridDef->addArgument($pagerDef);
            }

            $container->setDefinition($serviceId, $gridDef);
        }
    }

    public function getAlias()
    {
        return 'zenstruck_datagrid';
    }
}
