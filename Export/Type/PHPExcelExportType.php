<?php

namespace Zenstruck\DataGridBundle\Export\Type;

use Zenstruck\DataGridBundle\Field\Field;
use Zenstruck\DataGridBundle\Grid;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class PHPExcelExportType implements ExportTypeInterface
{
    public function __construct()
    {
        if (!class_exists('PHPExcel')) {
            throw new \Exception(sprintf('PHPExcel must be available to use the "%s" exporter.', $this->getType()));
        }
    }

    public function export(Grid $grid, $filename, $options = array())
    {
        $grid->execute();
        $options = array_merge(array(
                'date_format' => 'Y-m-d',
                'creator' => 'Kevin Bond',
                'title' => sprintf('%s Export', ucfirst($grid->getName())),
                'description' => '',
                'subject' => '',
                'company' => 'Zenstruck'
            ),
            $options
        );

        /** @var Field[] $fields */
        $fields = $grid->getFields()->getVisible();

        $phpExcel = new \PHPExcel();
        $phpExcel->setActiveSheetIndex(0);
        $properties = new \PHPExcel_DocumentProperties();
        $properties->setCreator($options['creator']);
        $properties->setLastModifiedBy($options['creator']);
        $properties->setTitle($options['title']);
        $properties->setDescription($options['description']);
        $properties->setSubject($options['subject']);
        $properties->setCompany($options['company']);
        $phpExcel->setProperties($properties);

        $i = 0;
        $j = 1;

        // headers
        foreach ($fields as $field) {
            $this->writeData($phpExcel, $i, $j, $field->getName());
            $i++;
        }

        // body
        foreach ($grid->getResults() as $result) {
            $i = 0;
            $j++;
            foreach ($fields as $field) {
                $this->writeData($phpExcel, $i, $j, $field->getValue($result));
                $i++;
            }
        }

        $writer = $this->getWriter($phpExcel);
        $writer->save($filename);

        return $filename;
    }

    protected function writeData(\PHPExcel $phpExcel, $column, $row, $data)
    {
        // column number to letter (1 = A)
        // @link http://studiokoi.com/blog/article/converting_numbers_to_letters_quickly_in_php
        $letter = chr(($column % 26) + 97);
        $letter .= (floor($column/26) > 0) ? str_repeat($letter, floor($column/26)) : '';

        $cellCoordinate = sprintf('%s%d', strtoupper($letter), $row);
        $cell = $phpExcel->getActiveSheet()->getCell($cellCoordinate);

        if ($data instanceof \DateTime) {
            $data = \PHPExcel_Shared_Date::PHPToExcel($data);
            $phpExcel->getActiveSheet()->getStyleByColumnAndRow($column, $row)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
        } elseif (is_numeric($data)) {
            $cell->setDataType(\PHPExcel_Cell_DataType::TYPE_NUMERIC);
        } else {
            $data = (string) $data;
            $cell->setDataType(\PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $cell->setValue($data);
    }

    /**
     * @param \PHPExcel $phpExcel
     *
     * @return \PHPExcel_Writer_IWriter
     */
    abstract public function getWriter(\PHPExcel $phpExcel);
}
