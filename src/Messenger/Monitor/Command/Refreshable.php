<?php

namespace App\Messenger\Monitor\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
trait Refreshable
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title(self::title());

        if (!$output instanceof ConsoleOutputInterface || !$input->isInteractive()) {
            return $this->render($io, $input);
        }

        $io = new SymfonyStyle($input, $section = $output->section());

        while (true) {
            $this->render($io, $input);
            $io->writeln('');
            $io->writeln('<comment>! [NOTE] Press CTRL+C to quit</comment>');

            \sleep(1);
            $section->clear();
        }
    }

    abstract private function render(SymfonyStyle $io, InputInterface $input): int;

    abstract private static function title(): string;
}
