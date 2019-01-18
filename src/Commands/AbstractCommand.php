<?php
namespace Articstudio\PhpBin\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Articstudio\PhpBin\Ui\Menu;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Articstudio\PhpBin\PhpBinException;

abstract class AbstractCommand extends SymfonyCommand
{
    
    use \Articstudio\PhpBin\Concerns\HasOutput;
    use \Articstudio\PhpBin\Concerns\HasShell;
    use \Articstudio\PhpBin\Concerns\HasPhpBin;

    /**
     * Create menu
     *
     * @param string $title
     * @param array $options
     * @return Menu
     */
    public function menu(string $title, array $options): Menu
    {
        return new Menu($title, $options);
    }

    /**
     * Call command by name
     *
     * @param string $name
     * @param array $arguments
     * @param OutputInterface $output
     * @return int
     */
    protected function callCommandByName(string $name, ?array $arguments, OutputInterface $output)
    {
        if (!$this->getApplication()->has($name)) {
            throw new PhpBinException("Command `{$name}` not found.");
        }
        $command = $this->getApplication()->get($name);
        $input = new ArrayInput($arguments ?? []);
        return $command->run($input, $output);
    }
}
