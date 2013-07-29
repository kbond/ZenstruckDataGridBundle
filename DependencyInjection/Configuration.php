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
                ->arrayNode('export')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('base_dir')->defaultValue('%kernel.cache_dir%/export')->end()
                        ->variableNode('global_options')->defaultValue(array())->end()
                        ->arrayNode('types')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('csv')->defaultTrue()->end()
                                ->booleanNode('xls')->defaultTrue()->end()
                                ->booleanNode('xlsx')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
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
            ->scalarNode('grid_class')->defaultNull()->end()
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
                        ->variableNode('filter_values')
                            ->defaultNull()
                            ->validate()
                                ->ifTrue(function($value) {
                                    return !is_null($value) && !is_array($value);
                                })
                                ->thenInvalid('Must be either null or an array.')
                            ->end()
                        ->end()
                        ->booleanNode('searchable')->defaultFalse()->end()
                        ->booleanNode('sortable')->defaultFalse()->end()
                        ->scalarNode('sort_direction')
                            ->defaultNull()
                            ->validate()
                                ->ifNotInArray(Field::getAvailableSortDirections())
                                ->thenInvalid(sprintf('sort_direction must be one of: %s', implode(', ', array_map(function($value) {
                                        return is_null($value) ? 'null' : sprintf('"%s"', $value);
                                    }, Field::getAvailableSortDirections()))))
                            ->end()
                        ->end()
                        ->scalarNode('format')->defaultNull()->end()
                        ->scalarNode('align')->defaultNull()->end()
                        ->scalarNode('default')->defaultNull()->end()
                    ->end()
                ->end()
        ;
    }
}
