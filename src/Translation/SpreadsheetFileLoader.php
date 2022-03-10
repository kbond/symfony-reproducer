<?php

namespace App\Translation;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\SheetInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Loader\FileLoader;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SpreadsheetFileLoader extends FileLoader
{
    protected function loadResource(string $resource): array
    {
        return \iterator_to_array(self::parse($resource));
    }

    private static function parse(string $file): \Traversable
    {
        try {
            $reader = ReaderEntityFactory::createReaderFromFile($file);
            $reader->open($file);
        } catch (UnsupportedTypeException|IOException $e) {
            throw new InvalidResourceException($e->getMessage(), 0, $e);
        }

        foreach ($reader->getSheetIterator() as $sheet) {
            /** @var SheetInterface $sheet */
            foreach ($sheet->getRowIterator() as $row) {
                /** @var Row $row */
                $cells = $row->toArray();

                if (isset($cells[0], $cells[1]) && $cells[0] && $cells[1]) {
                    yield $cells[0] => $cells[1];
                }
            }
        }

        $reader->close();
    }
}
