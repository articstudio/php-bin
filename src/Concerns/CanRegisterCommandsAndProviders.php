<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Concerns;

trait CanRegisterCommandsAndProviders {

    /**
     * Available providers
     *
     * @var array
     */
    protected $providers = [
        '\Articstudio\PhpBin\Commands\Php\PhpProvider',
        '\Articstudio\PhpBin\Commands\Composer\ComposerProvider',
        '\Articstudio\PhpBin\Commands\Git\GitProvider',
    ];

    /**
     * Default application command
     *
     * @var string
     */
    protected $default_command = '\Articstudio\PhpBin\Commands\Menu';

    /**
     * Register available commands
     *
     * @return \self
     */
    protected function registerCommands(): self
    {
        $commands = array_merge(
            $this->getCommands(),
            ($this->composer_settings['commands'] ?? [])
        );
        array_map(function ($class_name) {
            $this->resgiterCommand($class_name);
        }, $commands);
        return $this;
    }

    /**
     * Register command
     *
     * @param string $class_name
     *
     * @return PhpBinCommand
     */
    protected function resgiterCommand(string $class_name): PhpBinCommand
    {
        if (! class_exists($class_name) || ! is_subclass_of($class_name, PhpBinCommand::class)) {
            throw new \Articstudio\PhpBin\PhpBinException("Incompatible command class: {$class_name}");
        }
        $command = new $class_name();
        $command->injectPhpBin($this);
        $this->console->add($command);
        return $command;
    }

    /**
     * Register default command
     *
     * @return \self
     */
    private function registerDefaultCommand(): self
    {
        $command = $this->resgiterCommand(
            $this->default_command
        );
        $this->console->setDefaultCommand($command->getName());
        return $this;
    }

    /**
     * Register available providers
     *
     * @return \self
     */
    private function registerProviders(): self
    {
        $commands = array_merge(
            $this->providers,
            ($this->composer_settings['providers'] ?? [])
        );
        array_map(function ($class_name) {
            $this->resgiterProvider($class_name);
        }, $commands);
        return $this;
    }

    /**
     * Register provider
     *
     * @param string $class_name
     *
     * @return ProviderContract
     */
    private function resgiterProvider(string $class_name): ProviderContract
    {
        if (! class_exists($class_name) || ! is_subclass_of($class_name, ProviderContract::class)) {
            throw new \Articstudio\PhpBin\PhpBinException("Incompatible provider class: {$class_name}");
        }
        $provider = new $class_name();
        $provider->injectPhpBin($this);
        $provider->register();
        return $provider;
    }
}
