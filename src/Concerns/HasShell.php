<?php declare(strict_types = 1);

namespace Articstudio\PhpBin\Concerns;

use Articstudio\PhpBin\Application;
use Symfony\Component\Process\Process;

trait HasShell
{

    /**
     * Call shell command
     *
     * @param string $cmd
     * @param bool $throw
     * @param int $timeout
     * @return array
     * @throws ProcessFailedException
     */
    protected function callShell(string $cmd, bool $throw = true, $timeout = 300): array
    {
        $composer = Application::getInstance()->getComposer();
        $cmd = "cd {$composer['directory']} && " . $cmd;
        $process = new Process($cmd);
        $process->setTimeout($timeout);
        $process->run();
        if ($throw && ! $process->isSuccessful()) {
            throw new \Symfony\Component\Process\Exception\ProcessFailedException($process);
        }
        return [
            $process->getExitCode(),
            $process->getOutput(),
            $process->getExitCodeText(),
            $process->getErrorOutput(),
        ];
    }
}
