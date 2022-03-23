<?php

namespace App\Translation\Command;

use App\Translation\TranslationManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenstruck\Collection\ArrayCollection;

#[AsCommand(
    name: 'zenstruck:object-translator:warm-cache',
    description: 'Warm object translation cache',
)]
final class WarmObjectTranslationCacheCommand extends Command
{
    private array $alternateLocales;

    public function __construct(
        private TranslationManager $translationManager,
        array $enabledLocales,
        string $defaultLocale,
    ) {
        $this->alternateLocales = ArrayCollection::for($enabledLocales)
            ->combineWithSelf()
            ->unset($defaultLocale)
            ->values()
            ->all()
        ;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Locales to warm', $this->alternateLocales)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$locales = $input->getOption('locale')) {
            throw new \InvalidArgumentException('You must pass at least one locale with --locale or set "framework.enabled_locales" config.');
        }

        $io->title(\sprintf('Warming Object Translation Cache for Locales: %s', implode(', ', $locales)));

        foreach ($io->progressIterate($this->translationManager->translatableObjects()) as $object) {
            foreach ($locales as $locale) {
                $this->translationManager->proxyFor($object, $locale, forceRefresh: true);
            }
        }

        $io->success('Done.');

        return self::SUCCESS;
    }
}
