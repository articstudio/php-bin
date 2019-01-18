<?php
namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Providers\AbstractProvider;

class PhpProvider extends AbstractProvider
{

    /**
     * Available commands
     * 
     * @var array
     */
    protected $commands = [
        '\Articstudio\PhpBin\Commands\Php\Menu',
        '\Articstudio\PhpBin\Commands\Php\Lint',
        '\Articstudio\PhpBin\Commands\Php\Metrics',
        '\Articstudio\PhpBin\Commands\Php\Psr1',
        '\Articstudio\PhpBin\Commands\Php\Psr1Fix',
        '\Articstudio\PhpBin\Commands\Php\Psr2',
        '\Articstudio\PhpBin\Commands\Php\Psr2Fix',
        '\Articstudio\PhpBin\Commands\Php\Test',
    ];
}
