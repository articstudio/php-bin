<?php
namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Install extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'composer install';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'composer:install';
}
