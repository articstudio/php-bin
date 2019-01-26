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
            $input_package_name = is_int($user_choice) ? array_keys($packages)[ $user_choice ] : $user_choice;
            $isMenu             = true;
        }

        if ($input_package_name === null || $input_package_name === false) {
            return 1;
        }

        $input_repository = $packages[ $input_package_name ] ?? null;

        if ($input_package_name === 'new') {
            list( $input_package_name, $input_repository, $input_store ) = $this->showNewPackageQuestions();
        }

        if ($input_store) {
            $this->addSubtreeToComposer(array( $input_package_name => $input_repository ));
            $this->commitChanges("Add subtree " . $input_package_name);
        }

        if (! $isMenu && ! $this->checkPackageInComposer($input_package_name)) {
            throw new PhpBinException('Package ' . $input_package_name . ' configuration not found');
        }

        $txt = $this->addGitSubtree($input_package_name, $input_repository);
        $io->writeln($txt);

        return 1;
    }

    protected function commitChanges(string $message)
    {
        $cmd = 'git commit -m "' . $message . '" composer.json';

        list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell($cmd, false);

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
        $store          = $force_store === null ? $this->confirmation('Store this package/repository to the Composer config? ') : $force_store;

        return [ $package_name, $git_repository, $store ];
    }

    protected function addGitSubtree($package_name, $git_repository)
    {
        $cmd_remote_add  = 'git remote add ' . $package_name . ' ' . $git_repository;
        $cmd_add_subtree = 'git subtree add --prefix=' . $package_name . '/ ' . $git_repository . ' master';

        $this->callShell($cmd_remote_add, false);
        list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell($cmd_add_subtree, false);

        if ($exit_code === 1) {
            throw new PhpBinException('Error adding the package ' . $package_name . ' subtree from ' . $git_repository . '');
        }

        $error_msg = $exit_code_txt . "\n" . $error;

        return $output !== '' ? $output : $error_msg;
    }
}
