<?php

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends PhpBinCommand
{

    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasComposerConfig;
    use \Articstudio\PhpBin\Commands\Git\Subtree\Concerns\HasSubtreesConfig;


    protected $io;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'composer:install';

    protected function configure()
    {
        $this->addArgument('package_name', InputArgument::OPTIONAL, 'Nom del package:');
        $this->addArgument('module_name', InputArgument::OPTIONAL, 'Nom del mòdul:');
        $this->addArgument('envoirment', InputArgument::OPTIONAL, 'Entorn:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $modules            = [];
        $this->io           = $this->getStyle($output, $input);
        $composer           = $this->getComposerData();
        $input_package_name = $input->getArgument('package_name') ?: null;
        $input_module_name  = $input->getArgument('module_name') ?: null;
        $env                = $input->getArgument('envoirment') ?: null;
        $composer_dir       = $this->getComposerPath();

        if ($input_package_name === null || $input_module_name === null) {
            //MENU
            [$input_package_name, $input_module_name, $env] = $this->showNewPackageQuestions();
        } else {
            $modules[] = $input_module_name;
        }

        $composer_module_file = $composer_dir . '/' . $input_module_name . '/composer.json';

        if (! file_exists($composer_module_file)) {
            throw new \Articstudio\PhpBin\PhpBinException('composer.json file not found: ' . $composer_module_file);
        }

        $version = $this->searchPackageVersion($input_package_name, $composer);
        $version = $this->requireDevPackage($version, $input_package_name);

        $this->addPackageToComposerRequire([$input_package_name => $version], $composer_module_file, $env);

        $this->io->success("Package " . $input_module_name . " succesfully installed");

        return $this->exit($output, 0);
    }

    protected function showNewPackageQuestions()
    {
        $package_name = $this->io->ask('Please enter the name of the package to install: ');
        $module_name  = $this->io->choice(
            'Select a module where you want to install the package',
            array_keys($this->getSubtrees())
        );
        $env          = $this->io->confirm('Do you want save this package in require-dev? ') ? 'd' : null;

        return [$package_name, $module_name, $env];
    }

    private function requireDevPackage($version, $input_package_name)
    {

        if (! $version) {
            try {
                $command = 'composer require --dev ' . $input_package_name;
                [$exit_code, $output, $exit_code_txt, $error] = $this->callShell($command, false);
                if ($exit_code === 1) {
                    throw new \Articstudio\PhpBin\PhpBinException("Error installing package: " . $input_package_name . " " . $error);
                }
                $composer = json_decode(file_get_contents($this->getComposerFile()), true);
                $version  = $this->searchPackageVersion($input_package_name, $composer);
            } catch (\Articstudio\PhpBin\PhpBinException $exception) {
                $this->io->error('Caught exception package: ', $exception->getMessage());
                exit(1);
            }
        }

        if (! $version) {
            try {
                throw new \Articstudio\PhpBin\PhpBinException("Package not found: " . $input_package_name);
            } catch (\Articstudio\PhpBin\PhpBinException $exception) {
                $this->io->error("Caught exception package: " . $exception->getMessage());
                exit(1);
            }
        }

        return $version;
    }

    private function searchPackageVersion($search_package, $package_json)
    {
        $result = false;

        if (key_exists($search_package, $package_json['require'])) {
            $result = $package_json['require'][$search_package];
        } elseif (key_exists($search_package, $package_json['require-dev'])) {
            $result = $package_json['require-dev'][$search_package];
        }

        return $result;
    }
}
