<?php
namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Lint extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'php ./vendor/bin/parallel-lint . --exclude vendor --exclude build --colors';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:lint';
}
