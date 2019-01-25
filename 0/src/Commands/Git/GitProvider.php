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
    ];
}
