<?php

namespace App\Translation;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class XlsxFileDumper extends SpreadsheetFileDumper
{
    protected function createWriter(): WriterInterface
    {
        return WriterEntityFactory::createXLSXWriter();
    }

    protected function extension(): string
    {
        return 'xlsx';
    }
}
