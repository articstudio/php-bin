<?php

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Normalize extends AbstractCommand {

    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasComposerConfig;
    use Concerns\HasComposerBehaviour;
    use \Articstudio\PhpBin\Commands\Git\Subtree\Concerns\HasSubtreesConfig;

    protected $composer;
    protected $io;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'composer:normalize';

    protected function configure() {
        $this->addArgument('module_name', InputArgument::OPTIONAL, 'Nom del mòdul:');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->io = $this->getStyle($output, $input);
        $this->composer = $this->getComposerData();
        $module_dir = $input->getArgument('module_name') ?: null;
        $options = array_keys($this->getSubtrees()) + [
            'root' => 'Composer project',
            'all' => 'All modules',
        ];

        $option = ($module_dir === null) ? $this->selectPackageMenu(
                        "Normalize composer",
                        $options
                ) : null;

        $this->io->title('Composer normalized');
        if ($option === 'back') {
            return $this->callCommandByName('composer:menu', [], $output);
        }

        $modules = ($module_dir === null) ? $this->getModulesByOption($option) : [$module_dir];

        foreach ($modules as $module_name) {
            $output_messages = array_map(function ($name) {
                return $this->normalizeComposerFile($name);
            }, $this->getComposerJson($module_name));

            $this->showResultMessages($output_messages, $module_name);
        }

        return $this->exit($output, 0);
    }

    private function normalizeComposerFile($fname) {
        $command = 'composer normalize --no-update-lock ' . $fname;

        [$exit_code, $output, $exit_code_txt, $error] = $this->callShell($command, false);

        if ($exit_code === 1) {
            throw new \Articstudio\PhpBin\PhpBinException("Error normalize composer file of : " . $fname . ' ' . $error);
        }

        return ($exit_code === 0) ? $output : [];
    }

    private function showResultMessages(array $messages, string $module_name) {
        $this->io->section($module_name . ", normalize messages: ");
        if (count($messages) < 1) {
            $this->io->writeln("Not composer.json found");
            return;
        }
        foreach ($messages as $message) {
            $this->io->writeln("\t" . $message);
        }
    }

}
