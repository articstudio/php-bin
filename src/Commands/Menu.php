<?php
namespace Articstudio\PhpBin\Commands;

use Articstudio\PhpBin\Commands\AbstractMenuCommand as PhpBinMenuCommand;

class Menu extends PhpBinMenuCommand
{

    /**
     * Menu options
     *
     * @var array
     */
    protected $menuOptions = [
        'git' => 'Git',
        'php' => 'PHP',
        'composer' => 'Composer'
    ];

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'phpbin';
    
    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setAliases([
            'menu'
        ]);
    }
}
