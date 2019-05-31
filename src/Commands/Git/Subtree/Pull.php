<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Pull extends AbstractCommand
{

    use Concerns\HasSubtreesConfig;
    use Concerns\HasSubtreeBehaviour;

    protected $io;
    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'git:subtree:pull';

    protected function configure()
    {
        $this->addArgument('package_name', InputArgument::IS_ARRAY, 'Nom del package:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repositories  = $this->getSubtrees();
        $this->io      = $this->getStyle($output, $input);
        $package_names = $input->getArgument('package_name') ?: [];

        if (count($package_names) < 1) {
            $menu_options = array_keys($repositories) + [
                'all' => 'All subtrees',
            ];
            $option       = $this->selectPackageMenu('Pull subtree', $menu_options);

            if ($option === 'back') {
                return $this->callCommandByName('git', [], $output);
            }

            if ($option === null || $option === false) {
                return 1;
            }

            $package_names = is_int($option) ? [array_keys($repositories)[$option]] :
                ($option === 'all' ? array_keys($repositories) : []);
        }

        $local_changes = $this->getLocalChanges();
        if ($local_changes) {
            $ask_commit     = "You need to commit changes before pull a subtree. ";
            $commit_message = $this->io->ask($ask_commit . " \n Commit message: ", "wip");
            $commited       = $commit_message ? $this->commitChanges(
                $commit_message,
                '-a'
            ) : false;
        }

        $result = $this->subtreePull($repositories, $package_names);
        $this->showResume($result, $this->io);

        return $this->exit($output, 0);
    }

    private function subtreePull(array $repositories, $package_names)
    {
        $result = [
            'skipped'   => [],
            'done'      => [],
            'error'     => [],
            'not_found' => [],
        ];
        foreach ($repositories as $repo_package => $repo_url) {
            if (count($package_names) < 1 || in_array($repo_package, $package_names)) {
                if (! $this->subtreeExists($repo_package)) {
                    $result['not_found'][] = $repo_package;
                    unset($repositories[$repo_package]);
                    continue;
                }
                $cmd = 'git subtree pull --prefix=' . $repo_package . '/ ' . $repo_package . ' master --squash';
                [$exit_code, $output, $exit_code_txt, $error] = $this->callShell($cmd, false);
                $key            = $exit_code === 0 ? 'done' : 'error';
                $result[$key][] = $repo_package;
                continue;
            }
            $result['skipped'][] = $repo_package;
        }

        return $result;
    }
}
