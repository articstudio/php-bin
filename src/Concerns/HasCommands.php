<?php
namespace Articstudio\PhpBin\Concerns;

trait HasCommands
{
    
    /**
     * Add comand to available commands
     * 
     * @param string $command
     * @return int
     */
    public function addComand(string $command): int
    {
        $this->checkCommands();
        return array_push($this->commands, $command);
    }
    
    /**
     * Get available commands
     * 
     * @return array
     */
    public function getCommands(): array
    {
        $this->checkCommands();
        return $this->commands;
    }
    
    /**
     * Check available commands attribute
     */
    protected function checkCommands()
    {
        if (!is_array($this->commands))
        {
            $this->commands = [];
        }
    }
    
}
