<?php
namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Psr1 extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'php ./vendor/bin/phpcs '
        . '--standard=PSR1 --colors '
        . '--ignore=*/vendor/*,*/build/*,*/resources/* ./';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:psr1';
}
