<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 21/01/19
 * Time: 14:28
 */
namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Check extends PhpBinCommand
{

    use Concerns\HasSubtreesConfig;

    protected static $defaultName = 'git:subtree:check';

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = $this->getStyle($output, $input);
        $cmd_subtrees_git = "git log"
            ." | grep git-subtree-dir"
            ." | tr -d ' '"
            ." | cut -d \":\" -f2"
            ." | sort"
            ." | uniq"
            ." | xargs -I {} bash -c 'if [ -d $(git rev-parse --show-toplevel)/{} ] ; then echo {}; fi'";
        $subtrees_composer = $this->getSubtrees();
        list( $exit_code, $subtrees_git, $exit_code_txt, $error ) = $this->callShell($cmd_subtrees_git, true);

        $io->writeln("Composer subtrees: ");
        if (isset($subtrees_composer)) {
            foreach ($subtrees_composer as $name => $url) {
                $io->writeln("\t" . $name . ' => ' . $url);
            }
        }

        $io->writeln("");

        $io->writeln("Git subtrees: ");
        if (isset($subtrees_git)) {
            $io->writeln("\t" . $subtrees_git);
        }
        $io->writeln("");
    }
}
