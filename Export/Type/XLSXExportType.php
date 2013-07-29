<?php

namespace Zenstruck\DataGridBundle\Export\Type;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class XLSXExportType extends PHPExcelExportType
{
    public function getType()
    {
        return 'xlsx';
    }

    public function getWriter(\PHPExcel $phpExcel)
    {
        return new \PHPExcel_Writer_Excel2007($phpExcel);
    }
}