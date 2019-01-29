<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Articstudio\PhpBin\PhpBinException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends PhpBinCommand
{

    use \Articstudio\PhpBin\Concerns\HasWriteComposer;
    use Concerns\HasSubtreesConfig;
    use Concerns\HasSubtreeBehaviour;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'git:subtree:add';

    protected function configure()
    {
        $this->addArgument('package_name', InputArgument::OPTIONAL, 'Nom del package:');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $packages           = $this->getSubtrees();
        $io                 = $this->getStyle($output, $input);
        $input_package_name = $input->getArgument('package_name') ?: null;
        $input_repository   = null;
        $input_store        = null;
        $isMenu             = false;
        if ($input_package_name === null) {
            $menu_options       = array_keys($packages) + [
                    'new' => 'New package'
                ];
            $user_choice        = $this->showMenu("Subtree packages", $menu_options);
            $input_package_name = is_int($user_choice) ? array_keys($packages)[$user_choice] : $user_choice;
            $isMenu             = true;
        }

        if ($input_package_name === null || $input_package_name === false) {
            return 1;
        }

        $input_repository = $packages[$input_package_name] ?? null;

        if ($input_package_name === 'new') {
            list($input_package_name, $input_repository, $input_store) = $this->showNewPackageQuestions();
        }

        if ($input_store) {
            $this->addSubtreeToComposer(array($input_package_name => $input_repository));
            $this->getLocalChanges() === true ? $this->commitChanges("Add subtree " . $input_package_name . '" composer.json', '-a') : false;
        }
        die;

        if ( ! $isMenu && ! $this->checkPackageInComposer($input_package_name)) {
            throw new PhpBinException('Package ' . $input_package_name . ' configuration not found');
        }

        $txt = $this->addGitSubtree($input_package_name, $input_repository);
        $io->writeln($txt);

        return 1;
    }

    protected function commitChanges(string $message, string $files)
    {
        $cmd = 'git commit -m "' . $message . '" ' . $files;

        list($exit_code, $output, $exit_code_txt, $error) = $this->callShell($cmd, false);

        var_dump($output);
        var_dump($exit_code);
        if ($exit_code === 1) {
            throw new PhpBinException('Error commit ' . $message);
        }
        $error_msg = $exit_code_txt . "\n" . $error;

        return $output !== '' ? $output : $error_msg;
    }

    protected function showNewPackageQuestions(?bool $force_store = null)
    {
        $package_name   = $this->question('Please enter the name of the package: ');
        $git_repository = $this->question('Please enter the URL of the git repository: ');
        $store          = $force_store;
        if ($store === null) {
            $store = $this->confirmation('Store this package/repository to the Composer config? ');
        }

        return [$package_name, $git_repository, $store];
    }

    protected function askCommit(string $message)
    {
        return $this->confirmation($message);
    }

    protected function addGitSubtree($package_name, $git_repository)
    {

        $local_changes = $this->getLocalChanges();
        if ($local_changes) {
            $ask_commit     = "Do you want to commit changes before? (y/n) ";
            $commit_message = $this->askCommit($ask_commit) ? $this->question("Commit message: ") : false;
            $commited       = $commit_message ? $this->commitChanges($commit_message,
                '-a') : false;
            if ( ! $commited) {
                throw new PhpBinException(
                    'Error adding the package '
                    . $package_name
                    . ' subtree from '
                    . $git_repository
                    . ' because have local changes to commit.'
                );
            }
        }

        if($this->subtreeExists($package_name)) {
            throw new PhpBinException(
                'Error adding the package '
                . $package_name
                . ' subtree from '
                . $git_repository
                . '. It already exists'
            );
        }

        $cmd_remote_add  = 'git remote add ' . $package_name . ' ' . $git_repository;
        $cmd_add_subtree = 'git subtree add --prefix=' . $package_name . '/ ' . $git_repository . ' master';

        $this->callShell($cmd_remote_add, false);
        list($exit_code, $output, $exit_code_txt, $error) = $this->callShell($cmd_add_subtree, false);

        if ($exit_code === 1) {
            throw new PhpBinException(
                'Error adding the package '
                . $package_name
                . ' subtree from '
                . $git_repository
                . ''
            );
        }

        $error_msg = $exit_code_txt . "\n" . $error;

        return $output !== '' ? $output : $error_msg;
    }
}
