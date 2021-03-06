<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Remove extends Command {

    use Concerns\HasSubtreesConfig;
    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasSubtreeBehaviour;

    protected $io;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'git:subtree:remove';

    protected function configure() {
        $this->addArgument('package_name', InputArgument::IS_ARRAY, 'Nom del package:');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->io = $this->getStyle($output, $input);
        $repositories = $this->getSubtrees();
        $input_store = null;
        $package_names = $input->getArgument('package_name') ?? [];

        if (count($package_names) < 1) {
            $menu_options = array_keys($repositories) + [
                'all' => 'All subtrees',
            ];
            $option = $this->selectPackageMenu('Remove Subtrees', $menu_options);

            if ($option === 'back') {
                return $this->callCommandByName('git', [], $output);
            }

            if ($option === null) {
                return $this->exit($output, 1);
            }

            $package_names = is_int($option) ? [array_keys($repositories)[$option]] :
                    ($option === 'all' ? array_keys($repositories) : []);
        }

        $result = $this->removeDirAndRemoteSubtree($repositories, $package_names);

        $input_store = $this->showNewPackageQuestions();

        if ($input_store) {
            $this->removeSubtreeToComposer($package_names);
        }

        $this->showResume($result, $this->io);

        return $this->exit($output, 0);
    }

    protected function showNewPackageQuestions(?bool $force_store = null) {
        if ($force_store === null) {
            $force_store = $this->confirmation('Remove this package/repository of the Composer config? (y/n) ');
        }

        return $force_store;
    }

    private function removeDirAndRemoteSubtree(array $repositories, array $package_names) {

        $result = [
            'skipped' => [],
            'done' => [],
            'error' => [],
            'not_found' => [],
        ];

        foreach (array_keys($repositories) as $repo_package) {
            if (count($package_names) > 0 && ! in_array($repo_package, $package_names, true)) {
                $result['skipped'][] = $repo_package;
                continue;
            }
            if (! $this->subtreeExists($repo_package)) {
                $result['not_found'][] = $repo_package;
                unset($repositories[$repo_package]);
                continue;
            }
            $cmd = 'git remote rm ' . $repo_package;
            $this->callShell($cmd, false);
            $cmd = 'git rm -r ' . $repo_package . '/';
            $this->callShell($cmd, false);
            $cmd = 'rm -r ' . $repo_package . '/';
            $this->callShell($cmd, false);
            $cmd = 'git commit -m "Removing ' . $repo_package . ' subtree"';
            [$exit_code] = $this->callShell($cmd, false);
            $key = $exit_code === 0 ? 'done' : 'error';
            $result[$key][] = $repo_package;
        }

        return $result;
    }

}
