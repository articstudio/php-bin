<?php declare(strict_types = 1);

namespace Articstudio\PhpBin\Providers;

abstract class AbstractProvider implements ProviderInterface
{

    use \Articstudio\PhpBin\Concerns\HasCommands;
    use \Articstudio\PhpBin\Concerns\HasPhpBin;

    /**
     * Register provider
     *
     * @return ProviderInterface
     */
    public function register(): ProviderInterface
    {
        $this->addCommandsToApplication(
            $this->getCommands()
        );
        return $this;
    }

    /**
     * Add commands to application
     *
     * @param array $commands
     * @return \self
     */
    private function addCommandsToApplication(array $commands): self
    {
        array_map(function ($class_name) {
            $this->phpbin->addComand($class_name);
        }, $commands);
        return $this;
    }
}
