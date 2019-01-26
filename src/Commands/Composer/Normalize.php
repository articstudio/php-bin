<?php

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

use Articstudio\PhpBin\PhpBinException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Normalize extends PhpBinShellCommand
{
    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasComposerConfig;
    use Concerns\HasComposerBehaviour;
    use \Articstudio\PhpBin\Commands\Git\Subtree\Concerns\HasSubtreesConfig;

    protected $composer;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'composer:normalize';

    protected function configure()
    {
        $this->addArgument('module_name', InputArgument::OPTIONAL, 'Nom del mòdul:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->composer = $this->getComposerData();
        $io             = $this->getStyle($output, $input);
        $module_dir     = $input->getArgument('module_name') ?: null;
        $menu_options   = array(
            'select' => 'Select a single module',
            'all'    => 'All modules',
            'root'   => 'Composer project'
        );

        if ($module_dir === null) {
            $option  = $this->showMenu("Normalize composer", $menu_options);
            $modules = $this->getModulesByOption($option);
        } else {
            $modules[] = $module_dir;
        }

        foreach ($modules as $module_name) {
            $output_messages      = array_map(function ($name) {
                return $this->normalizeComposerFile($name);
            }, $this->getComposerJson($module_name));

            $this->showResultMessages($output_messages, $io);
        }
    }

    private function normalizeComposerFile($fname)
    {
        $command = 'composer normalize --no-update-lock ' . $fname;

        list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell($command, false);

        if ($exit_code === 1) {
            throw new PhpBinException("Error normalize composer file of : " . $fname);
        }

        return ( $exit_code === 0 ) ? $output : [];
    }

    private function showResultMessages(array $messages, SymfonyStyle $output)
    {
        $output->writeln("Normalize messages: ");
        if (!empty($messages)) {
            foreach ($messages as $message) {
                $output->writeln("\t" . $message);
            }
        } else {
            $output->writeln("Not composer.json found");
        }
    }
}
