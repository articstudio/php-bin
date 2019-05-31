<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Providers\Provider;

class PhpProvider extends Provider
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
        '\Articstudio\PhpBin\Commands\Php\Style',
        '\Articstudio\PhpBin\Commands\Php\StyleFix',
        '\Articstudio\PhpBin\Commands\Php\Insights',
        '\Articstudio\PhpBin\Commands\Php\Test',
    ];
}
