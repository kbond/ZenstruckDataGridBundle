<?php

namespace Zenstruck\DataGridBundle\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Field
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';

    protected $name;
    protected $label;
    protected $visible;
    protected $filterable;
    protected $filterValue;
    protected $filterValues;
    protected $sortable;
    protected $sortDirection;
    protected $format;
    protected $align;
    protected $default;
    protected $searchable;

    public static function getAvailableSortDirections()
    {
        return array(null, static::SORT_ASC, static::SORT_DESC);
    }

    public function __construct($name, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
                'label' => null,
                'visible' => true,
                'filterable' => false,
                'filter_value' => null,
                'filter_values' => null,
                'sortable' => false,
                'sort_direction' => null,
                'format' => null,
                'align' => null,
                'default' => null,
                'searchable' => false
            ));
        $resolver->setAllowedTypes(array(
                'label' => array('string', 'null'),
                'visible' => 'bool',
                'filterable' => 'bool',
                'filter_value' => array('string', 'null'),
                'filter_values' => array('null', 'array'),
                'sortable' => 'bool'
            ));
        $resolver->setAllowedValues(array(
                'sort_direction' => static::getAvailableSortDirections()
            ));
        $options = $resolver->resolve($options);

        $this->name = $name;
        $this->label = $options['label'];
        $this->visible = $options['visible'];
        $this->filterable = $options['filterable'];
        $this->filterValue = $options['filter_value'];
        $this->filterValues = $options['filter_values'];
        $this->sortable = $options['sortable'];
        $this->sortDirection = $options['sort_direction'];
        $this->format = $options['format'];
        $this->align = $options['align'];
        $this->default = $options['default'];
        $this->searchable = $options['searchable'];
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return $this->filterable;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @return bool
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @return string|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string|null
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * @return string|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function setFilterValue($value)
    {
        if (!$this->isFilterable() || ((is_array($this->filterValues) && !in_array($value, $this->filterValues)))) {
            $this->filterValue = null;

            return;
        }

        $this->filterValue = $value;
    }

    /**
     * @return array|null
     */
    public function getFilterValues()
    {
        return $this->filterValues;
    }

    /**
     * @return string
     */
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    /**
     * @param $value
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function setSortDirection($value)
    {
        if (!$this->isSortable() || null === $value) {
            $this->sortDirection = null;

            return;
        }

        $value = strtolower($value);

        if (in_array($value, static::getAvailableSortDirections())) {
            $this->sortDirection = $value;
        }
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    public function getOppositeSortDirection()
    {
        return in_array($this->sortDirection, array(null, static::SORT_ASC)) ? static::SORT_DESC : static::SORT_ASC;
    }

    /**
     * @param object|array $object
     *
     * @return mixed
     */
    public function getValue($object)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $name = $this->getName();

        if (is_array($object)) {
            $name = sprintf('[%s]', $name);
        }

        return $accessor->getValue($object, $name);
    }
}
