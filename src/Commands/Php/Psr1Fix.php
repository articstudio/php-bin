<?php
namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Psr1Fix extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'php ./vendor/bin/phpcbf '
        . '--standard=PSR1 --colors '
        . '--ignore=*/vendor/*,*/build/*,*/resources/*,*/test*/* ./';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:psr1:fix';
}
