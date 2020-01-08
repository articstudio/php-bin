<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Commands\ShellCommand as PhpBinShellCommand;

class Lint extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'php ./vendor/bin/phpstan analyse --ansi';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:lint';
}
