<?php

namespace App\Translation\Command;

use App\Translation\Model\Translation;
use App\Translation\TranslationManager;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'zenstruck:object-translator:export',
    description: 'Export translatable objects in the default locale',
)]
final class ExportTranslatableObjectsCommand extends Command
{
    private const VALID_FORMATS = ['csv', 'xlsx', 'ods'];

    public function __construct(
        private TranslationManager $translationManager,
        private string $defaultLocale,
        private string $defaultFilename,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, \sprintf('Export format (%s)', implode(', ', self::VALID_FORMATS)), 'csv')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Export file', $this->defaultFilename)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!\interface_exists(WriterInterface::class)) {
            throw new \RuntimeException('box/spout required to export translations: composer require box/spout');
        }

        $io = new SymfonyStyle($input, $output);
        $format = $input->getOption('format');
        $filename = \strtr($input->getOption('file'), ['{format}' => $format]);
        $writer = $this->writer($format)->openToFile($filename);

        $io->title(\sprintf('Exporting Default Locale (%s) to %s', $this->defaultLocale, $format));

        $io->comment('Exporting to: '.$filename);

        $count = 0;

        foreach ($io->progressIterate($this->translationManager->translatableObjects()) as $object) {
            $collection = $this->translationManager->findOrCreateFor($object, $this->defaultLocale);

            foreach ($collection as $property => $translation) {
                /** @var Translation $translation */
                $ref = new \ReflectionProperty($object, $property);
                $ref->setAccessible(true);

                if (null === $value = $ref->getValue($object)) {
                    // don't export null fields
                    continue;
                }

                $row = $translation
                    ->setValue($value)
                    ->toArray()
                ;

                if (0 === $count) {
                    // headers
                    $writer->addRow(WriterEntityFactory::createRowFromArray(\array_keys($row)));
                }

                $writer->addRow(WriterEntityFactory::createRowFromArray($row));
            }

            $count += $collection->count();
        }

        if (0 === $count) {
            throw new \RuntimeException('No translations found.');
        }

        $writer->close();

        $io->success(\sprintf('Done. %d translations exported.', $count));

        return self::SUCCESS;
    }

    private function writer(string $format): WriterInterface
    {
        return match ($format) {
            'xlsx' => WriterEntityFactory::createXLSXWriter(),
            'ods' => WriterEntityFactory::createODSWriter(),
            'csv' => WriterEntityFactory::createCSVWriter(),
            default => throw new \InvalidArgumentException(\sprintf('"%s" is not a valid export format.', $format)),
        };
    }
}
