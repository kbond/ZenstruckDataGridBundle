<?php

namespace Zenstruck\DataGridBundle\Export\Type;

use Zenstruck\DataGridBundle\Grid;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface ExportTypeInterface
{
    /**
     * @param Grid   $grid
     * @param string $filename
     * @param array  $options
     *
     * @return string The filename
     */
    public function export(Grid $grid, $filename, $options = array());

    /**
     * @return string
     */
    public function getType();
}
