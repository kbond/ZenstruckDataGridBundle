<?php

namespace Zenstruck\DataGridBundle;

use Zenstruck\DataGridBundle\Executor\ExecutorInterface;
use Zenstruck\DataGridBundle\Field\FieldCollection;
use Zenstruck\DataGridBundle\Filter\FilterInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @Serializer\ExclusionPolicy("none")
 */
class Grid
{
    protected $name;
    protected $parameters = array();
    protected $fields;
    protected $results;

    /**
     * @Serializer\Exclude
     *
     * @var FilterInterface
     */
    protected $filter;

    /**
     * @Serializer\Exclude
     *
     * @var ExecutorInterface
     */
    protected $executor;

    /**
     * @Serializer\Exclude
     *
     * @var bool
     */
    protected $executed = false;

    public function __construct(
        $name,
        FieldCollection $fields,
        FilterInterface $filter,
        ExecutorInterface $executor
    )
    {
        $this->name = $name;
        $this->fields = $fields;
        $this->filter = $filter;
        $this->executor = $executor;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return FieldCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        $this->ensureExecuted();

        return $this->results;
    }

    /**
     * @return Grid
     */
    public function execute()
    {
        if ($this->executed) {
            return $this;
        }

        $this->results = $this->executor->execute($this->filter->filter($this->getFields()));
        $this->executed = true;

        return $this;
    }

    /**
     * @throws \RuntimeException
     */
    public function ensureExecuted()
    {
        if (!$this->executed) {
            throw new \RuntimeException('Must run ->execute() on Grid.');
        }
    }

    /**
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }
}