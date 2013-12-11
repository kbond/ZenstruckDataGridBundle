<?php

namespace Zenstruck\DataGridBundle\Export\Type;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class XLSExportType extends PHPExcelExportType
{
    public function getType()
    {
        return 'xls';
    }

    public function getWriter(\PHPExcel $phpExcel)
    {
        return new \PHPExcel_Writer_Excel5($phpExcel);
    }
}
