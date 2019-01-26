<?php
namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Providers\AbstractProvider;

class ComposerProvider extends AbstractProvider
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
