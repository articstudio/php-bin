<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\Command as PhpBinCommand;
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

        $cmd_subtrees_git = implode(' | ', [
            "git log",
            "grep git-subtree-dir",
            "tr -d ' '",
            "cut -d \":\" -f2",
            "sort",
            "uniq",
            "xargs -I {} bash -c 'if [ -d $(git rev-parse --show-toplevel)/{} ] ; then echo {}; fi'",
        ]);

        $subtrees_composer = array_keys($this->getSubtrees());
        [, $subtrees_git, ,  ] = $this->callShell($cmd_subtrees_git, true);

        $subtrees_git = array_filter(explode("\n", $subtrees_git), static function ($value): bool {
            return $value !== '';
        });

        $composer_and_subtree = array_intersect($subtrees_composer, $subtrees_git);

        $this->writeSubtreeInfo($composer_and_subtree);

        $this->io->newLine();

        $this->io->section("Only composer: ");
        $this->writeSubtreeInfo(array_diff($subtrees_composer, $subtrees_git));

        $this->io->newLine();

        $this->io->section("Only subtrees: ");
        $this->writeSubtreeInfo(array_diff($subtrees_git, $composer_and_subtree));

        return $this->exit($output, 0);
    }

    private function writeSubtreeInfo(array $subtree): void
    {
        foreach ($subtree as $name) {
            $this->io->writeln($name);
        }
    }
}
