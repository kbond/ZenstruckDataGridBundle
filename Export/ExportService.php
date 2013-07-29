<?php

namespace Zenstruck\DataGridBundle\Export;

use Zenstruck\DataGridBundle\Export\Type\ExportTypeInterface;
use Zenstruck\DataGridBundle\Grid;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExportService
{
    /** @var ExportTypeInterface[] */
    protected $types = array();
    protected $baseDir;
    protected $options;

    public function __construct($baseDir, $options = array())
    {
        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        $this->baseDir = $baseDir;
        $this->options = $options;
    }

    public function export(Grid $grid, $type, $filename = null, $options = array())
    {
        $options = array_merge($this->options, $options);

        if (!isset($this->types[$type])) {
            throw new \RuntimeException(sprintf('The exporter "%s" has not been added to the factory.', $type));
        }

        $exporter = $this->types[$type];

        if (!$filename) {
            $filename = sprintf('%s/%s.%s', $this->baseDir, uniqid('export'), $type);
        }

        return $exporter->export($grid, $filename, $options);
    }

    public function addExportType(ExportTypeInterface $exportType)
    {
        $this->types[$exportType->getType()] = $exportType;
    }
}