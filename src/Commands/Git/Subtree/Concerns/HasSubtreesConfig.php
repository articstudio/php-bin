<?php

declare(strict_types=1);

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

    public function getVersionsGroups(): array
    {
        $composer = Application::getInstance()->getComposer();
        $config   = $composer['data']['config'] ?? [];

        return $config['versions'] ?? [];
    }

    public function getPackageVersion(): ?string
    {
        $composer = Application::getInstance()->getComposer();
        return $composer['data']['version'] ?? null;
    }

    public function checkPackageInComposer(string $package_name)
    {
        $subtrees = $this->getSubtrees();

        return isset($subtrees[$package_name]) ?? false;
    }

    public function getLocalChanges()
    {
        $cmd = 'git diff --exit-code';
        [$exit_code, , , ] = $this->callShell($cmd, false);

        return $exit_code === 0 ? false : true;
    }

    public function commitChanges(string $message, string $files)
    {
        $cmd = 'git commit -m "' . $message . '" ' . $files;

        [$exit_code, $output, $exit_code_txt, $error] = $this->callShell($cmd, false);

        if ($exit_code === 1) {
            throw new \Articstudio\PhpBin\PhpBinException('Error commit ' . $message);
        }
        $error_msg = $exit_code_txt . "\n" . $error;

        return $output !== '' ? $output : $error_msg;
    }
}
