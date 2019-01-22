<?php
namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Update extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'composer update';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'composer:update';
}
