<?php

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Composer\Semver\Constraint\Constraint;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetDevPackages extends PhpBinCommand
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
    protected static $defaultName = 'composer:dev-packages';

    protected function configure()
    {
        $this->addArgument('module_name', InputArgument::OPTIONAL, 'Nom del mòdul:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->composer = $this->getComposerData();
        $packages       = $this->getSubtrees();
        $module_dir     = $input->getArgument('module_name') ?: null;
        $menu_options   = array_keys($packages) + [
                'all' => 'All modules'
            ];

        if ($module_dir === null) {
            $option  = $this->showMenu("Update packages versions", $menu_options);
            $modules = $this->getModulesByOption($option);
        } else {
            $modules[] = $module_dir;
        }

        if (! key_exists('require', $this->composer)) {
            $this->composer['require'] = [];
        }
        if (! key_exists('require-dev', $this->composer)) {
            $this->composer['require-dev'] = [];
        }
        foreach ($modules as $module_name) {
            array_map(function ($name) {
                $this->mergeDependencies($name);
            }, $this->getComposerJson($module_name));
        }


        $this->writeComposer($this->composer, $this->getComposerFile());
    }

    protected function addDependencies($dependencies, $fname)
    {
        if (! $dependencies) {
            return;
        }
        foreach ($dependencies as $dependency => $version) {
            if (! key_exists($dependency, $this->composer['require']) && ! key_exists(
                $dependency,
                $this->composer['require-dev']
            )) {
                $this->composer['require-dev'][$dependency] = $version;
                printf("  + %s@%s \n", $dependency, $version);
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
                printf("  = %s@%s \n", $dependency, $version);
                continue;
            }
            if (key_exists(
                $dependency,
                $this->composer['require-dev']
            ) && $this->composer['require-dev'][$dependency] < $version) {
                $this->composer['require-dev'][$dependency] = $version;
            }
            printf("  ! %s@%s \n", $dependency, $version);
        }
    }

    private function mergeDependencies($fname)
    {
        printf("%s: \n", $fname);
        $data = json_decode(file_get_contents($fname), true);
        if (key_exists('require', $data)) {
            $this->addDependencies($data['require'], $fname);
        }
        if (key_exists('require-dev', $data)) {
            $this->addDependencies($data['require-dev'], $fname);
        }
    }

    protected function showNewPackageQuestions()
    {
        return $this->question(
            'Please enter the name of the module where you want to get the require/require-dev packages: '
        );
    }
}
