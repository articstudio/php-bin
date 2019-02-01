<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Push extends AbstractCommand
{

    use Concerns\HasSubtreesConfig;
    use Concerns\HasSubtreeBehaviour;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'git:subtree:push';

    protected function configure()
    {
        $this->addArgument('package_name', InputArgument::IS_ARRAY, 'Nom del package:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repositories = $this->getSubtrees();

        $package_names = $input->getArgument('package_name') ?: array();

        if (empty($package_names)) {
            $menu_options = array_keys($repositories) + [
                    'all' => 'All subtrees'
                ];
            $option       = $this->selectPackageMenu('Push subtrees', $menu_options);

            if ($option === 'back') {
                return $this->callCommandByName('git', [], $output);
            }

            if ($option === null) {
                return 1;
            }

            $package_names = is_int($option) ? array(array_keys($repositories)[$option]) :
                ($option === 'all' ? array_keys($repositories) : array());
        }

        $result = $this->pushSubtree($repositories, $package_names);
        $this->showResume($result);

        return 1;
    }

    private function pushSubtree(array $repositories, $package_names)
    {
        $result = array(
            'skipped'   => [],
            'done'      => [],
            'error'     => [],
            'not_found' => [],
        );

        foreach ($repositories as $repo_package => $repo_url) {
            if (empty($package_names) || in_array($repo_package, $package_names)) {
                if (! $this->subtreeExists($repo_package)) {
                    $result['not_found'][] = $repo_package;
                    unset($repositories[$repo_package]);
                    continue;
                }
                $cmd = 'git subtree push --prefix=' . $repo_package . '/ ' . $repo_url . ' master';
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
