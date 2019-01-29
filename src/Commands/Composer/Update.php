<?php

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends PhpBinShellCommand
{

    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasComposerConfig;
    use Concerns\HasComposerBehaviour;
    use \Articstudio\PhpBin\Commands\Git\Subtree\Concerns\HasSubtreesConfig;

    protected $composer;
    protected $versions;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'composer:update-versions';

    protected function configure()
    {
        $this->addArgument('module_name', InputArgument::OPTIONAL, 'Nom del mòdul:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->composer = $this->getComposerData();
        $packages       = $this->getSubtrees();
        $this->versions = array_merge($this->composer['require-dev'], $this->composer['require']);
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


        foreach ($modules as $module_name) {
            array_map(function ($name) {
                $this->overrideAllDependenciesVersions($name);
            }, $this->getComposerJson($module_name));
        }
    }

    private function replaceDependenciesVersions($obj)
    {
        $result = [];
        foreach ($obj as $package => $version) {
            if (key_exists($package, $this->versions)) {
                $result[$package] = $this->versions[$package] ?: $obj[$package];
                $symbol = $this->versions[$package] === $obj[$package] ? '=' : '+';
                printf($symbol . "%s@%s \n", $package, $result[$package]);
            }
        }

        return $result;
    }

    private function overrideAllDependenciesVersions($fname)
    {
        printf("%s: \n", $fname);

        $this->composer = json_decode(file_get_contents($fname), true);

        $requires_dev   = array(
            'require-dev' => array()
        );
        $this->composer = array_merge($this->composer, $requires_dev);

        printf(">> require-dev \n");
        $this->composer['require-dev'] = $this->replaceDependenciesVersions($this->composer['require-dev']);
        printf(">> require \n");
        $this->composer['require'] = $this->replaceDependenciesVersions($this->composer['require']);


        $this->writeComposer($this->composer, $fname);
    }

    protected function showNewPackageQuestions()
    {
        return $this->question('Please enter the name of the module where you want to solve the versions problems: ');
    }
}
