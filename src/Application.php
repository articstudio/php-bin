<?php

declare(strict_types=1);

namespace Articstudio\PhpBin;

use Symfony\Component\Console\Application as SymfonyConsole;

final class Application
{

    use Concerns\HasOutput;
    use Concerns\HasCommands;
    use Concerns\HasComposer;
    use Concerns\CanRegisterCommandsAndProviders;

    /**
     * PHPBIN version
     *
     * @var string
     */
    private static $version = '2.0.0';

    /**
     * Singleton self instance
     *
     * @var \self
     */
    private static $instance;

    /**
     * Symfony console instance
     *
     * @var SymfonyConsole
     */
    private $console;

    /**
     * Private constructor
     */
    private function __construct(SymfonyConsole $console)
    {
        $this->console = $console;
        $this->console->setCatchExceptions(false);
    }

    /**
     * Private clonator
     */
    private function __clone()
    {
    }

    /**
     * Execution
     */
    public static function exec()
    {
        try {
            if (self::getInstance()) {
                throw new \Exception('Application is alredy executed.');
            }
            $instance = new self(
                new SymfonyConsole('phpbin', self::$version)
            );
            self::$instance = $instance;
            $instance->prepare()
                ->run();
        } catch (\Exception $e) {
            self::$instance->throwError(null, $e->getMessage(), $e->getTraceAsString(), 1, true);
        }
    }

    /**
     * Singleton constructor
     *
     * @return \self|null
     */
    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    /**
     * Prepare console application
     *
     * @return \self
     */
    private function prepare(): self
    {
        return $this->discoverComposer()
            ->parseComposerSettings()
            ->registerProviders()
            ->registerDefaultCommand()
            ->registerCommands();
    }

    /**
     * Run console
     *
     * @return void
     */
    private function run(): void
    {
        $this->console->run();
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::$version;
    }
}
