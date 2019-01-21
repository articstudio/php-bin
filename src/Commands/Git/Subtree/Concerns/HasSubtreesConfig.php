<?php
namespace Articstudio\PhpBin\Commands\Git\Subtree\Concerns;

use Articstudio\PhpBin\Application;

trait HasSubtreesConfig
{

    public function getSubtrees(): array
    {
        $composer = Application::getInstance()->getComposer();
        $config = $composer['data']['config'] ?? [];
        return $config['subtree'] ?? [];
    }
}
