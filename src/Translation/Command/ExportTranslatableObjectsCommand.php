<?php

namespace App\Translation\Command;

use App\Translation\Attribute\Translatable;
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

        dump(['locale', 'object', 'object_id', 'field', 'value']);

        foreach ($io->progressIterate($this->translationManager->translatableObjects()) as $object) {
            /* @var object $object */
            $alias = Translatable::for($object::class)->alias ?? $object::class;

            foreach (Translatable::propertiesFor($object::class) as $property => $attribute) {
                /* @var \ReflectionProperty $property */
                /* @var Translatable $attribute */

                $property->setAccessible(true);

                dump([
                    $this->defaultLocale,
                    $alias,
                    $this->translationManager->idFor($object),
                    $attribute->alias ?? $property->name,
                    $property->getValue($object),
                ]);
            }
        }

        $io->success('Done.');

        return self::SUCCESS;
    }
}
