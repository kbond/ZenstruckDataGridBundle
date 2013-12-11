<?php

namespace Zenstruck\DataGridBundle\Twig;

use Zenstruck\DataGridBundle\Field\Field;
use Zenstruck\DataGridBundle\Filter\RequestFilter;
use Zenstruck\DataGridBundle\Grid;
use Zenstruck\DataGridBundle\PaginatedGrid;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GridExtension extends \Twig_Extension
{
    /** @var \Twig_Environment */
    protected $environment;

    /** @var \Twig_Template[] */
    protected $templates = array();

    protected $defaultTemplate;

    public function __construct($defaultTemplate)
    {
        $this->defaultTemplate = $defaultTemplate;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('grid', array($this, 'renderGrid'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_header', array($this, 'renderHeader'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_body', array($this, 'renderBody'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_footer', array($this, 'renderFooter'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_label', array($this, 'renderLabel'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_value', array($this, 'renderValue'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_pager', array($this, 'renderPager'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_action_label', array($this, 'renderActionLabel'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_action', array($this, 'renderAction'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('grid_no_results', array($this, 'renderNoResults'), array('is_safe' => array('html')))
        );
    }

    public function renderGrid(Grid $grid, \Twig_Template $theme = null)
    {
        $this->setTemplate($theme);
        $grid->execute();

        return $this->renderBlock('grid', array('grid' => $grid));
    }

    public function renderLabel(Field $field, Grid $grid)
    {
        $params = array('field' => $field, 'grid' => $grid);

        // custom block
        $block = sprintf('grid_label_%s', $field->getName());

        if (!$this->hasBlock($block)) {
            $block = 'grid_label';
        }

        return $this->renderBlock($block, $params);
    }

    public function renderValue($object, Field $field, Grid $grid)
    {
        $value = $field->getValue($object);
        $params = array('value' => $value, 'object' => $object, 'field' => $field, 'grid' => $grid, 'type' => null);

        // custom block
        $block = sprintf('grid_value_%s', $field->getName());

        if ($this->hasBlock($block)) {
            return $this->renderBlock($block, $params);
        }

        if ($value instanceof \DateTime) {
            $params['type'] = 'date';
        }

        return $this->renderBlock('grid_value', $params);
    }

    public function renderNoResults(Grid $grid)
    {
        return $this->renderBlock('grid_no_results', array('grid' => $grid));
    }

    public function renderAction($object, Grid $grid)
    {
        return $this->renderBlock('grid_action', array('object' => $object, 'grid' => $grid));
    }

    public function renderActionLabel(Grid $grid)
    {
        return $this->renderBlock('grid_action_label', array('grid' => $grid));
    }

    public function renderPager(Grid $grid)
    {
        $pager = null;
        $pagerParams = array();

        if ($grid instanceof PaginatedGrid) {
            $pager = $grid->getPager();

            if (($filter = $grid->getFilter()) instanceof RequestFilter) {
                $pagerParams = array(
                    'routeName' => $filter->getRoute(),
                    'routeParams' => $filter->getRouteParams()
                );
            }
        }

        return $this->renderBlock('grid_pager', array(
                'pager' => $pager,
                'grid' => $grid,
                'pager_params' => $pagerParams
            ));
    }

    public function renderHeader(Grid $grid)
    {
        return $this->renderBlock('grid_header', array('grid' => $grid));
    }

    public function renderBody(Grid $grid)
    {
        return $this->renderBlock('grid_body', array('grid' => $grid));
    }

    public function renderFooter(Grid $grid)
    {
        $params = array('grid' => $grid, 'paginated' => false);

        if ($grid instanceof PaginatedGrid) {
            $params['paginated'] = true;
        }

        return $this->renderBlock('grid_footer', $params);
    }

    public function getName()
    {
        return 'zenstruck_grid';
    }

    protected function renderBlock($name, array $parameters = array())
    {
        foreach ($this->getTemplates() as $template) {
            if ($template->hasBlock($name)) {
                return $template->renderBlock($name, array_merge($this->environment->getGlobals(), $parameters));
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Block "%s" doesn\'t exist in template "%s".', $name,
                implode(', ', array_map(function (\Twig_Template $template) {
                            return $template->getTemplateName();
                        }, $this->getTemplates()
                    )))
        );
    }

    protected function hasBlock($name)
    {
        foreach ($this->getTemplates() as $template) {
            if ($template->hasBlock($name)) {
                return true;
            }
        }

        return false;
    }

    protected function setTemplate(\Twig_Template $template = null)
    {
        $this->templates = array();

        if ($template instanceof \Twig_Template) {
            $this->templates[] = $template;
        }

        if ($this->environment->hasExtension('pagerfanta')) {
            try {
                $this->templates[] = $this->environment->loadTemplate(str_replace('blocks', 'pagerfanta', $this->defaultTemplate));
            } catch (\Twig_Error_Loader $e) {
                //skip
            }
        }

        $this->templates[] = $this->environment->loadTemplate($this->defaultTemplate);
    }

    /**
     * @return \Twig_Template[]
     */
    protected function getTemplates()
    {
        return $this->templates;
    }
}
