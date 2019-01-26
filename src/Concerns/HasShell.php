<?php
namespace Articstudio\PhpBin\Concerns;

use Articstudio\PhpBin\Application;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

trait HasShell
{

    /**
     * Call shell command
     *
     * @param string $cmd
     * @return array
     * @throws ProcessFailedException
     */
    protected function callShell(string $cmd, bool $throw = true): array
    {
        $composer = Application::getInstance()->getComposer();
        $cmd = "cd {$composer['directory']} && " . $cmd;
        $process = new Process($cmd);
        $process->run();
        //while ($process->isRunning()) {
        // TODO: Show loading spinner
        //}
        if ($throw && !$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return [
            $process->getExitCode(),
            $process->getOutput(),
            $process->getExitCodeText(),
            $process->getErrorOutput()
        ];
    }
}
