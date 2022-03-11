<?php

namespace App\Translation;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class OdsFileDumper extends SpreadsheetFileDumper
{
    protected function createWriter(): WriterInterface
    {
        return WriterEntityFactory::createODSWriter();
    }

    protected function extension(): string
    {
        return 'ods';
    }
}
