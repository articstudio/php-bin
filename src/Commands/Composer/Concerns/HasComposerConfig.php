<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 22/01/19
 * Time: 14:45
 */

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
