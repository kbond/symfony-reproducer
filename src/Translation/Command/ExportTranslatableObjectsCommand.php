<?php

namespace App\Translation\Command;

use App\Translation\TranslationManager;
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
    private const VALID_FORMATS = ['csv'];

    public function __construct(
        private TranslationManager $translationManager,
        private string $defaultLocale
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, \sprintf('Export format (%s)', implode(', ', self::VALID_FORMATS)), 'csv')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = $input->getOption('format');

        $io->title(\sprintf('Exporting Default Locale (%s) to %s', $this->defaultLocale, $format));

        $count = 0;

        foreach ($io->progressIterate($this->translationManager->translatableObjects()) as $object) {
            $collection = $this->translationManager->findOrCreateFor($object, $this->defaultLocale);

            foreach ($collection as $property => $translation) {
                $ref = new \ReflectionProperty($object, $property);
                $ref->setAccessible(true);

                $translation->value = $ref->getValue($object);

                $row = $translation->toArray();

                if (0 === $count) {
                    // headers
                    dump(\array_keys($row));
                }

                dump($row);
            }

            $count += $collection->count();
        }

        if (0 === $count) {
            throw new \RuntimeException('No translations found.');
        }

        $io->success(\sprintf('Done. %d translations exported.', $count));

        return self::SUCCESS;
    }
}
