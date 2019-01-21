<?php
namespace Articstudio\PhpBin\Commands;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Example extends PhpBinCommand
{

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'example';
    
    /**
     * Configure command
     */
    protected function configure()
    {
        // ...
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('This is an example command');
        return 0;
    }
}
