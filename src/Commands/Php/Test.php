<?php

namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Application;
use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends PhpBinCommand
{

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:test';

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = Application::getInstance()->getComposer();
        $io = $this->getStyle($output, $input);
        $output->writeln("Executing command at `{$composer['directory']}`");
        $files = glob("{$composer['directory']}/*phpunit.xml");
        if (count($files) < 1) {
            $io->comment("No PHPunit XML files fount at `{$composer['directory']}`");
            return $this->exit($output);
        }
        foreach ($files as $file) {
            $io->title("Test suite: `{$file}`");
            [
                $exitCode,
                $str,
                $str_error_message,
                $str_error_trace,
            ] = $this->callShell("php ./vendor/bin/phpunit --configuration {$file}", false);
            if ($exitCode !== 0 && ($str_error_message || $str_error_trace)) {
                return $this->throwError($output, $str_error_message, $str_error_trace, $exitCode);
            }
            echo $str;
        }
        return $this->exit($output, 0);
    }
}
