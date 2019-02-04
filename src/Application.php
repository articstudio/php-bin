<?php
namespace Articstudio\PhpBin;

use Symfony\Component\Console\Application as SymfonyConsole;
use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Articstudio\PhpBin\Providers\ProviderInterface as ProviderContract;
use Exception;
use Articstudio\PhpBin\PhpBinException;

final class Application
{

    use Concerns\HasOutput;
    use Concerns\HasCommands;

    /**
     * PHPBIN version
     *
     * @var string
     */
    private static $version = '1.0.4';

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
     * Composer directory
     *
     * @var string
     */
    private $composer_dir;

    /**
     * Composer file
     *
     * @var string
     */
    private $composer_file;

    /**
     * Composer settings
     *
     * @var array
     */
    private $composer;

    /**
     * Settings
     *
     * @var array
     */
    private $settings;

    /**
     * Available providers
     *
     * @var array
     */
    private $providers = [
        '\Articstudio\PhpBin\Commands\Php\PhpProvider',
        '\Articstudio\PhpBin\Commands\Composer\ComposerProvider',
        '\Articstudio\PhpBin\Commands\Git\GitProvider',
    ];

    /**
     * Available commands
     *
     * @var array
     */
    protected $commands = [
        '\Articstudio\PhpBin\Commands\Example',
    ];

    /**
     * Default application command
     *
     * @var string
     */
    private $default_command = '\Articstudio\PhpBin\Commands\Menu';

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
        //
    }

    /**
     * Execution
     */
    public static function exec()
    {
        try {
            self::getInstance()
            ->prepare()
            ->run();
        } catch (Exception $e) {
            self::$instance->throwError(null, $e->getMessage(), $e->getTraceAsString(), 1, true);
        }
    }

    /**
     * Singleton constructor
     *
     * @return \self
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self(
                new SymfonyConsole('phpbin', static::$version)
            );
        }
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
                ->setSettings()
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
     * Discover composer.json
     *
     * @return \self
     */
    private function discoverComposer(): self
    {
        $this->composer_dir = dirname(
            dirname(PHPBIN_COMPOSER_AUTOLOAD)
        );
        $this->composer_file = realpath($this->composer_dir . '/' . 'composer.json');
        if (!is_readable($this->composer_file)) {
            throw new PhpBinException("composer.json not readable at `{$this->composer_dir}`");
        }
        $this->composer = [
            'directory' => $this->composer_dir,
            'file' => $this->composer_file,
            'data' => json_decode(file_get_contents($this->composer_file), true)
        ];
        return $this;
    }

    /**
     * Set settings
     *
     * @return \self
     */
    private function setSettings(): self
    {
        $config = $this->composer['data']['config'] ?? [];
        $this->settings = $config['phpbin'] ?? [];
        return $this;
    }

    /**
     * Register default command
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
            ($this->settings['providers'] ?? [])
        );
        array_map(function ($class_name) {
            $this->resgiterProvider($class_name);
        }, $commands);
        return $this;
    }

    /**
     * Resgiter provider
     *
     * @param string $class_name
     * @return ProviderContract
     */
    private function resgiterProvider(string $class_name): ProviderContract
    {
        if (!class_exists($class_name) || !is_subclass_of($class_name, ProviderContract::class)) {
            throw new PhpBinException("Incompatible provider class: {$class_name}");
        }
        $provider = new $class_name();
        $provider->setPhpBin($this);
        $provider->register();
        return $provider;
    }

    /**
     * Register available commands
     *
     * @return \self
     */
    private function registerCommands(): self
    {
        $commands = array_merge(
            $this->getCommands(),
            ($this->settings['commands'] ?? [])
        );
        array_map(function ($class_name) {
            $this->resgiterCommand($class_name);
        }, $commands);
        return $this;
    }

    /**
     * Resgiter command
     *
     * @param string $class_name
     * @return PhpBinCommand
     */
    private function resgiterCommand(string $class_name): PhpBinCommand
    {
        if (!class_exists($class_name) || !is_subclass_of($class_name, PhpBinCommand::class)) {
            throw new PhpBinException("Incompatible command class: {$class_name}");
        }
        $command = new $class_name();
        $command->setPhpBin($this);
        $this->console->add($command);
        return $command;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return static::$version;
    }

    /**
     * Get composer settings
     *
     * @return array
     */
    public function getComposer()
    {
        return $this->composer;
    }
}
