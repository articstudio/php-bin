<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Composer\Concerns;

use Articstudio\PhpBin\Application;

trait HasComposerConfig
{

    public function getComposerData()
    {
        return Application::getInstance()->getComposer()['data'] ?? [];
    }

    public function getComposerFile()
    {
        return Application::getInstance()->getComposer()['file'] ?? '';
    }

    public function getComposerPath()
    {
        return Application::getInstance()->getComposer()['directory'];
    }
}
