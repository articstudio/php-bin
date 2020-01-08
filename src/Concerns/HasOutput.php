<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Concerns;

use Articstudio\PhpBin\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

trait HasOutput
{

    /**
     * Throw error to console
     *
     * @param string $message
     * @param string|null $trace
     * @param int $exitCode
     * @param bool $forceExit
     */
    public function throwError(
        ?OutputInterface $output,
        string $message = 'ERROR',
        ?string $trace = null,
        int $exitCode = 1,
        bool $forceExit = false
    ) {
        $io = $this->getStyle($output);
        $io->error($message);
        if ((bool) $trace) {
            $io->text($trace);
        }
        return $this->exit($output, $exitCode, $forceExit);
    }

    /**
     * Get new SymfonyStyle instance
     *
     * @param OutputInterface|null $output
     * @param InputInterface|null $input
     *
     * @return SymfonyStyle
     */
    public function getStyle(?OutputInterface $output, ?InputInterface $input = null)
    {
        return new SymfonyStyle(
            ($input ?? new ArrayInput([])),
            ($output ?? new ConsoleOutput())
        );
    }

    /**
     * PHPBIN exit
     *
     * @param OutputInterface|null $output
     * @param int $exitCode
     * @param bool $force
     *
     * @return int
     */
    public function exit(?OutputInterface $output, int $exitCode = 0, bool $force = false): int
    {
        $output = $output ?? new ConsoleOutput();
        $version = Application::getInstance()->getVersion();
        $output->writeln(PHP_EOL . "Exit PHPBIN v{$version}");
        if ($force) {
            exit($exitCode);
        }
        return $exitCode;
    }
}
