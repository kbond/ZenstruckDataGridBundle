<?php

namespace Zenstruck\DataGridBundle\Export\Type;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class CSVExportType extends PHPExcelExportType
{
    public function getWriter(\PHPExcel $phpExcel)
    {
        return new \PHPExcel_Writer_CSV($phpExcel);
    }

    public function getType()
    {
        return 'csv';
    }
}