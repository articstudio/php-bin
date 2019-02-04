<?php
namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Check extends PhpBinCommand
{

    use Concerns\HasSubtreesConfig;

    protected $io;

    protected static $defaultName = 'git:subtree:check';

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->io = $this->getStyle($output, $input);
        $this->io->title('Diff subtrees');

        $this->io->section("Composer + subtree: ");

        $cmd_subtrees_git = "git log"
            ." | grep git-subtree-dir"
            ." | tr -d ' '"
            ." | cut -d \":\" -f2"
            ." | sort"
            ." | uniq"
            ." | xargs -I {} bash -c 'if [ -d $(git rev-parse --show-toplevel)/{} ] ; then echo {}; fi'";

        $subtrees_composer = $this->getSubtrees();
        list( $exit_code, $subtrees_git, $exit_code_txt, $error ) = $this->callShell($cmd_subtrees_git, true);

        $subtrees_git = array_filter(explode("\n", $subtrees_git), function ($value) {
            return $value !== '';
        });
        $composer_subtree = array_diff($subtrees_git, $subtrees_composer);

        $this->writeSubtreeInfo($composer_subtree);

        $this->io->newLine();

        $this->io->section("Only composer: ");
        $this->writeSubtreeInfo($subtrees_composer);

        $this->io->newLine();

        $this->io->section("Only subtrees: ");
        $this->writeSubtreeInfo($subtrees_git);

        return $this->exit($output, 0);
    }

    private function writeSubtreeInfo(array $subtree) {
        if (isset($subtree)) {
            foreach ($subtree as $name => $url) {
                $this->io->writeln($name);
            }
        }
    }
}
