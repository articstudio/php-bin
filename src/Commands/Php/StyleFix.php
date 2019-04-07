<?php
namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class StyleFix extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'php ./vendor/bin/phpcbf --colors ./';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:style:fix';
}