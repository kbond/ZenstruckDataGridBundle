<?php

namespace Zenstruck\DataGridBundle\Field;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FieldCollection implements \Countable, \IteratorAggregate
{
    /** @var Field[] */
    protected $fields = array();

    /**
     * @param array|Field[] $fields
     */
    public function __construct(array $fields = array())
    {
        foreach ($fields as $name => $field) {
            if (is_array($field)) {
                // is configuration array
                $field = new Field($name, $field);
            }

            $this->add($field);
        }
    }

    public function getVisible()
    {
        return array_filter($this->fields, function(Field $field) {
                return $field->isVisible();
            });
    }

    /**
     * @param array $filters
     *
     * @return $this
     */
    public function setFilterValues(array $filters = array())
    {
        foreach ($filters as $name => $value) {
            if ($this->has($name) && $this->get($name)->isFilterable()) {
                $field = $this->get($name);
                $field->setFilterValue($value);
            }
        }

        return $this;
    }

    /**
     * @param array $sorts
     *
     * @return $this
     */
    public function setSortDirections(array $sorts = array())
    {
        foreach ($sorts as $name => $direction) {
            if ($this->has($name) && $this->get($name)->isSortable()) {
                $field = $this->get($name);

                if (in_array($direction, Field::getAvailableSortDirections())) {
                    $field->setSortDirection($direction);
                }
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clearSorts()
    {
        return $this->forAll(function(Field $field) {
                if ($field->isSortable()) {
                    $field->setSortDirection(null);
                }
            }
        );
    }

    /**
     * @return $this
     */
    public function clearFilters()
    {
        return $this->forAll(function(Field $field) {
                if ($field->isFilterable()) {
                    $field->setFilterValue(null);
                }
            }
        );
    }

    /**
     * @param string $name
     *
     * @return Field
     *
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist', $name));
        }

        return $this->fields[$name];
    }

    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    public function add(Field $field)
    {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * @return Field[]
     */
    public function all()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    protected function forAll(\Closure $p)
    {
        foreach ($this->fields as $field) {
            $p($field);
        }

        return $this;
    }
}