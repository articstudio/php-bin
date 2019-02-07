<?php
namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Psr2Fix extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'php ./vendor/bin/phpcbf '
        . '--standard=PSR2 --colors '
        . '--ignore=*/vendor/*,*/build/*,*/resources/*,*/test*/* ./';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:psr2:fix';
}
