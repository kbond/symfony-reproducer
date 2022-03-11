<?php

namespace App\Translation;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\RuntimeException;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @source {@see \Symfony\Component\Translation\Dumper\FileDumper}
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class SpreadsheetFileDumper implements DumperInterface
{
    final public function dump(MessageCatalogue $messages, array $options = []): void
    {
        if (!\array_key_exists('path', $options)) {
            throw new InvalidArgumentException('The file dumper needs a path option.');
        }

        foreach ($messages->getDomains() as $domain) {
            $fullpath = $options['path'].'/'.$this->filename($domain, $messages->getLocale());

            if (!file_exists($fullpath)) {
                $directory = \dirname($fullpath);
                if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
                    throw new RuntimeException(sprintf('Unable to create directory "%s".', $directory));
                }
            }

            $writer = $this->createWriter();

            $writer->openToFile($fullpath);
            $writer->addRow(WriterEntityFactory::createRowFromArray(['Key', 'Value']));

            foreach ($messages->all($domain) as $key => $value) {
                $writer->addRow(WriterEntityFactory::createRowFromArray([$key, $value]));
            }

            $writer->close();
        }
    }

    abstract protected function createWriter(): WriterInterface;

    abstract protected function extension(): string;

    private function filename(string $domain, string $locale): string
    {
        return strtr('%domain%.%locale%.%extension%', [
            '%domain%' => $domain,
            '%locale%' => $locale,
            '%extension%' => $this->extension(),
        ]);
    }
}
