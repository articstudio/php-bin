<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Concerns;

trait HasCommands
{

    /**
     * Available commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Add command to available commands
     *
     * @param string $command
     *
     * @return int
     */
    public function addComand(string $command): int
    {
        return array_push($this->commands, $command);
    }

    /**
     * Get available commands
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }
}
