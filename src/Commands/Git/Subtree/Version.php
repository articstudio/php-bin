<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class Version extends Command
{

    use Concerns\HasSubtreesConfig;
    use Concerns\HasSubtreeBehaviour;

    protected $io;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'git:subtree:version';

    protected function configure()
    {
        $this->addOption('v', null, InputOption::VALUE_OPTIONAL, 'Versió:');
        $this->addArgument('package_name', InputArgument::IS_ARRAY, 'Nom del package:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repositories = $this->getSubtrees();
        $versions_groups = $this->getVersionsGroups();
        $this->io     = $this->getStyle($output, $input);

        $package_names = $input->getArgument('package_name') ?: [];
        $version = $input->getOption('version') ?: null;

        if (count($package_names) < 1) {
            $menu_options = array_keys($repositories) + [
                'all' => 'All subtrees',
            ];
            foreach (array_keys($versions_groups) as $group_name) {
                $menu_options['group:'.$group_name] = 'Group: ' . $group_name;
            }
            $option = $this->selectPackageMenu('Version subtrees', $menu_options);

            if ($option === 'back') {
                return $this->callCommandByName('git', [], $output);
            }

            if ($option === null) {
                return 1;
            }

            if ($option === 'all') {
                $package_names = array_keys($repositories);
            } else if (substr($option, 0, 6) === 'group:') {
                $group_name = substr($option, 6);
                $package_names = $versions_groups[$group_name] ?? [];
            } else {
                $package_names = is_int($option)
                        ? [array_keys($repositories)[$option]]
                        : [];
            }
        }
        
        if (!$version) {
            $version   = $this->io->ask('Please enter the new version', $this->getPackageVersion());
        }

        $result = $this->versionSubtrees($repositories, $package_names, $version);
        $this->showResume($result, $this->io);

        return $this->exit($output, 0);
    }

    private function versionSubtrees(array $repositories, $package_names, $version)
    {
        $result = [
            'skipped'   => [],
            'done'      => [],
            'error'     => [],
            'not_found' => [],
        ];

        foreach ($repositories as $repo_package => $repo_url) {
            if (count($package_names) < 1 || ! in_array($repo_package, $package_names)) {
                $result['skipped'][] = $repo_package;
                continue;
            }
            if (! $this->subtreeExists($repo_package)) {
                $result['not_found'][] = $repo_package;
                unset($repositories[$repo_package]);
                continue;
            }
            $key = $this->versionSubtree($repo_package, $repo_url, $version)
                    ? 'done' : 'error';
            $result[$key][] = $repo_package;
        }

        return $result;
    }
    
    private function versionSubtree($package_name, $repository, $version): bool
    {
        $tmp = '/tmp/phpbin-release';
        $cmds = [
            "rm -rf {$tmp}",
            "mkdir {$tmp}",
            "cd {$tmp}",
            "git clone {$repository}",
            "git checkout master",
            "git tag -a {$version} -m \"v{$version}\"",
            "git push origin --tags"
        ];
        $cmd = implode (' && ', $cmds);
        [$exit_code] = $this->callShell($cmd, false);
        if ($exit_code !== 0) {
            $this->callShell("rm -rf {$tmp}", false);
            return false;
        }
        return true;
    }
    
}
