<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Providers\Provider;

class ComposerProvider extends Provider
{

    /**
     * Available commands
     *
     * @var array
     */
    protected $commands = [
        '\Articstudio\PhpBin\Commands\Composer\Menu',
        '\Articstudio\PhpBin\Commands\Composer\Install',
        '\Articstudio\PhpBin\Commands\Composer\Update',
        '\Articstudio\PhpBin\Commands\Composer\Normalize',
        '\Articstudio\PhpBin\Commands\Composer\GetDevPackages',
    ];
}
