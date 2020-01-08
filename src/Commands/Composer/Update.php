<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{

    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasComposerConfig;
    use Concerns\HasComposerBehaviour;
    use \Articstudio\PhpBin\Commands\Git\Subtree\Concerns\HasSubtreesConfig;

    protected $composer;

    protected $versions;

    protected $io;

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
        $this->io       = $this->getStyle($output, $input);
        $this->composer = $this->getComposerData();
        $this->versions = array_merge($this->composer['require-dev'], $this->composer['require']);
        $module_dir     = $input->getArgument('module_name') ?? null;
        $options        = array_keys($this->getSubtrees()) + ['all' => 'All modules'];
        $option         = $module_dir === null ? $this->selectPackageMenu(
            "Update packages versions",
            $options
        ) : null;

        $this->io->title('Version conflicts solved');
        if ($option === 'back') {
            return $this->callCommandByName('composer:menu', [], $output);
        }

        $modules = $module_dir === null ? $this->getModulesByOption($option) : [$module_dir];

        foreach ($modules as $module_name) {
            array_map(function ($name): void {
                $this->overrideAllDependenciesVersions($name);
            }, $this->getComposerJson($module_name));
        }

        return $this->exit($output, 0);
    }

    private function replaceDependenciesVersions($obj)
    {
        $result = [];
        foreach (array_keys($obj) as $package) {
            if (key_exists($package, $this->versions)) {
                $result[$package] = $this->versions[$package] ?? $obj[$package];
                $symbol           = $this->versions[$package] === $obj[$package] ? '=' : '+';
                $symbol === '+' ? $this->io->writeln("<info> + " . $package . "@" . $result[$package] . "</info>")
                    : $this->io->writeln(" = " . $package . "@" . $result[$package]);
            }
        }

        return $result;
    }

    private function overrideAllDependenciesVersions($fname)
    {
        $this->io->section($fname);

        $this->composer = json_decode(file_get_contents($fname), true);

        $requires_dev   = [
            'require-dev' => [],
        ];
        $this->composer = array_merge($this->composer, $requires_dev);

        $this->io->writeln("---- REQUIRE DEV ----");
        $this->composer['require-dev'] = $this->replaceDependenciesVersions($this->composer['require-dev']);
        $this->io->newLine();
        $this->io->writeln("---- REQUIRE ----");
        $this->composer['require'] = $this->replaceDependenciesVersions($this->composer['require']);

        $this->writeComposer($this->composer, $fname);
    }
}
