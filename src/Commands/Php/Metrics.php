<?php declare(strict_types = 1);

namespace Articstudio\PhpBin\Commands\Php;

use Articstudio\PhpBin\Commands\AbstractShellCommand as PhpBinShellCommand;

class Metrics extends PhpBinShellCommand
{

    /**
     * Shell command
     *
     * @var string
     */
    protected $shellCommand = 'php ./vendor/bin/phpmetrics --report-html=\"./build/metrics\" ./';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'php:metrics';
}
