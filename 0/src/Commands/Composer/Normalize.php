<?php
namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Normalize extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'composer normalize';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'composer:normalize';
}
