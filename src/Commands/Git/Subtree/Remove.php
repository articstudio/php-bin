<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Remove extends AbstractCommand
{

    use Concerns\HasSubtreesConfig;
    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasSubtreeBehaviour;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'git:subtree:remove';

    protected function configure()
    {
        $this->addArgument('package_name', InputArgument::IS_ARRAY, 'Nom del package:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repositories  = $this->getSubtrees();
        $input_store   = null;
        $package_names = $input->getArgument('package_name') ?: array();

        if (empty($package_names)) {
            $menu_options = array_keys($repositories) + [
                    'all' => 'All subtrees'
                ];
            $option       = $this->selectPackageMenu('Remove Subtrees', $menu_options);

            if ($option === 'back') {
                return $this->callCommandByName('git', [], $output);
            }

            if ($option === null) {
                return $this->exit($output, 1);
            }

            $package_names = is_int($option) ? array(array_keys($repositories)[$option]) :
                ($option === 'all' ? array_keys($repositories) : array());
        }


        $result = $this->removeDirAndRemoteSubtree($repositories, $package_names);

        $input_store = $this->showNewPackageQuestions();

        if ($input_store) {
            $this->removeSubtreeToComposer($package_names);
        }

        $this->showResume($result);

        return $this->exit($output, 0);
    }

    protected function showNewPackageQuestions(?bool $force_store = null)
    {
        if ($force_store === null) {
            $force_store = $this->confirmation('Remove this package/repository of the Composer config? (y/n) ');
        }

        return $force_store;
    }

    private function removeDirAndRemoteSubtree(array $repositories, array $package_names)
    {

        $result = array(
            'skipped'   => [],
            'done'      => [],
            'error'     => [],
            'not_found' => []
        );

        foreach ($repositories as $repo_package => $repo_url) {
            if (empty($package_names) || in_array($repo_package, $package_names)) {
                if ( ! $this->subtreeExists($repo_package)) {
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
                list($exit_code, $output, $exit_code_txt, $error) = $this->callShell($cmd, false);
                $key            = $exit_code === 0 ? 'done' : 'error';
                $result[$key][] = $repo_package;
                continue;
            }
            $result['skipped'][] = $repo_package;
        }

        return $result;
    }
}
