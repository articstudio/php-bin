<?php

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetDevPackages extends AbstractCommand
{

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
    protected static $defaultName = 'composer:dev-packages';


    protected function configure()
    {
        $this->addArgument('module_name', InputArgument::OPTIONAL, 'Nom del mòdul:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io   = $this->getStyle($output, $input);
        $module_dir = $input->getArgument('module_name') ?: null;
        $options    = $this->getSubtrees() + array('all' => 'All submodules');
        $option     = ($module_dir === null) ? $this->selectPackageMenu("Update packages versions", $options) : null;

        if ($option === 'back') {
            return $this->callCommandByName('composer:menu', [], $output);
        }

        $modules = ($module_dir === null) ? $this->getModulesByOption($option) : [$module_dir];

        $this->composer = $this->getComposerData();
        $this->initComposerRequires();

        foreach ($modules as $module_name) {
            array_map(function ($name) {
                $this->mergeDependencies($name);
            }, $this->getComposerJson($module_name));
        }


        $this->writeComposer($this->composer, $this->getComposerFile());
    }


    protected function initComposerRequires()
    {
        if ( ! key_exists('require', $this->composer)) {
            $this->composer['require'] = [];
        }
        if ( ! key_exists('require-dev', $this->composer)) {
            $this->composer['require-dev'] = [];
        }
    }

    protected function addDependencies($dependencies, $fname)
    {
        if ( ! $dependencies) {
            return;
        }
        foreach ($dependencies as $dependency => $version) {
            if ( ! key_exists($dependency, $this->composer['require']) && ! key_exists(
                    $dependency,
                    $this->composer['require-dev']
                )) {
                $this->composer['require-dev'][$dependency] = $version;
                $this->io->success("  + " . $dependency . "@" . $version . "");
                //printf("  + %s@%s \n", $dependency, $version);
                continue;
            }
            if ((key_exists(
                     $dependency,
                     $this->composer['require-dev']
                 ) && $this->composer['require-dev'][$dependency] === $version)
                ||
                (key_exists(
                     $dependency,
                     $this->composer['require']
                 ) && $this->composer['require'][$dependency] === $version)) {
                //printf("  = %s@%s \n", $dependency, $version);
                $this->io->note("  = " . $dependency . "@" . $version . "");
                continue;
            }
            if (key_exists(
                    $dependency,
                    $this->composer['require-dev']
                ) && $this->composer['require-dev'][$dependency] < $version) {
                $this->composer['require-dev'][$dependency] = $version;
            }
            //printf("  ! %s@%s \n", $dependency, $version);
            $this->io->warning("  ! " . $dependency . "@" . $version . "");
        }
    }

    private function mergeDependencies($fname)
    {
        //printf("%s: \n", $fname);
        $this->io->text($fname);
        $data = json_decode(file_get_contents($fname), true);
        if (key_exists('require', $data)) {
            $this->addDependencies($data['require'], $fname);
        }
        if (key_exists('require-dev', $data)) {
            $this->addDependencies($data['require-dev'], $fname);
        }
    }

}
