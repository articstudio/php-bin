<?php
namespace Articstudio\PhpBin\Commands\Git;

use Articstudio\PhpBin\Providers\AbstractProvider;

class GitProvider extends AbstractProvider
{

    /**
     * Available commands
     *
     * @var array
     */
    protected $commands = [
        '\Articstudio\PhpBin\Commands\Git\Menu',
        '\Articstudio\PhpBin\Commands\Git\Subtree\Push',
        '\Articstudio\PhpBin\Commands\Git\Subtree\Pull',
        '\Articstudio\PhpBin\Commands\Git\Subtree\Add',
        '\Articstudio\PhpBin\Commands\Git\Subtree\Remove',
        '\Articstudio\PhpBin\Commands\Git\Subtree\Check',
    ];
}
