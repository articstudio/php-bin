<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands;

use Articstudio\PhpBin\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellCommand extends Command
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = '';

    /**
     * Throws exception on shell execution error
     *
     * @var bool
     */
    protected $throwShellError = false;

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composer = Application::getInstance()->getComposer();
        $output->writeln("Executing command at `{$composer['directory']}`");
        [
            $exitCode,
            $str,
            $str_error_message,
            $str_error_trace,
        ] = $this->callShell($this->shellCommand, $this->throwShellError);
        echo $str;
        if ($exitCode !== 0 && ($str_error_message || $str_error_trace)) {
            $this->throwError($output, $str_error_message, $str_error_trace, 1, true);
        }
        return $this->exit($output, $exitCode);
    }
}
