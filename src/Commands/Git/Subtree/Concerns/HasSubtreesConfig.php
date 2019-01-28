<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree\Concerns;

use Articstudio\PhpBin\Application;

trait HasSubtreesConfig
{

    public function getSubtrees(): array
    {
        $composer = Application::getInstance()->getComposer();
        $config   = $composer['data']['config'] ?? [];

        return $config['subtree'] ?? [];
    }

    public function checkPackageInComposer(string $package_name)
    {
        $subtrees = $this->getSubtrees();

        return isset($subtrees[$package_name]) ?? false;
    }

    public function getLocalChanges()
    {
        $cmd = 'git diff --exit-code';
        list($exit_code, $output, $exit_code_txt, $error) = $this->callShell($cmd, false);

        return $exit_code === 0 ? false : true;
    }
}
