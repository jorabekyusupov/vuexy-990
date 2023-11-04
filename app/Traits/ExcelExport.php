<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use OpenSpout\Common\Entity\Style\Style;
use Rap2hpoutre\FastExcel\FastExcel;


class ExcelExport
{
    public static function export($query, $callback = null)
    {
        ini_set('max_execution_time', 3600); //10 minutes
        set_time_limit(3600);
        return   self::getExport($query, $callback);

    }

    private static function getExport($query, $callback = null)
    {
        $header_style = (new Style())
            ->setFontBold();

        $rows_style = (new Style())
            ->setShouldWrapText(false)
            ->setFontSize(11);

        $path = storage_path('app/exportFiles/' . Carbon::now()->format('Y-m-d') . '/');
        File::ensureDirectoryExists($path);
        ob_end_clean(); // this
        ob_start();
        //streamDownload
        return (new FastExcel(self::exportGenerator($query)))
            ->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->download( 'orders.xls', $callback);
    }

    private static function exportGenerator($query): \Generator
    {
        foreach ($query->getFilteredQuery()->cursor() as $item) {
            yield $item;
        }
    }


}