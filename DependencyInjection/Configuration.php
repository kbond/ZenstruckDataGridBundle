<?php

namespace Zenstruck\DataGridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Zenstruck\DataGridBundle\Field\Field;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zenstruck_datagrid');

        $rootNode
            ->children()
                ->scalarNode('default_template')
                    ->defaultValue('ZenstruckDataGridBundle:Twitter:blocks.html.twig')
                    ->cannotBeEmpty()
                    ->info('The default template to use when using the twig grid() function.')
                ->end()
            ->end()
        ;

        $node = $rootNode
            ->children()
                ->arrayNode('grids')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('entity')
                            ->isRequired()
                            ->info('The entity (in the short notation) to create a grid for.')
                            ->example('AppBundle:Product')
                        ->end()
        ;

        $this->addGridConfig($node);

        return $treeBuilder;
    }

    public function addGridConfig(NodeParentInterface $node)
    {
        $node
            ->scalarNode('service_id')
                ->defaultNull()
                ->info('The service id for the generated grid. By default it is: "<bundle_prefix>.grid.<grid_name>".')
            ->end()
            ->scalarNode('executor_service')
                ->defaultNull()
                ->info('Customize the grid executor (must implement ExecutorInterface)')
            ->end()
            ->booleanNode('paginated')
                ->defaultTrue()
                ->info('Whether or not to use a paginated grid.')
            ->end()
            ->arrayNode('fields')
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('label')->defaultNull()->end()
                    ->booleanNode('visible')
                        ->defaultTrue()
                        ->info('Set false to hide on display (can still be filtered/sorted)')
                    ->end()
                    ->booleanNode('filterable')->defaultFalse()->end()
                    ->scalarNode('filter_value')->defaultNull()->end()
                    ->booleanNode('sortable')->defaultFalse()->end()
                    ->scalarNode('sort_direction')->defaultValue(Field::SORT_ASC)->end()
                    ->scalarNode('format')->defaultNull()->end()
                    ->scalarNode('align')->defaultNull()->end()
                    ->scalarNode('default')->defaultNull()->end()
                ->end()
            ->end()
        ;
    }
}
