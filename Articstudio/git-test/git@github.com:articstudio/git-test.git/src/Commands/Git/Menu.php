<?php
namespace Articstudio\PhpBin\Commands\Git;

use Articstudio\PhpBin\Commands\AbstractMenuCommand as PhpBinMenuCommand;

class Menu extends PhpBinMenuCommand
{
    
    /**
     * Menu title
     *
     * @var string
     */
    protected $menuTitle = 'Git';

    /**
     * Menu options
     *
     * @var array
     */
    protected $menuOptions = [
        'git:subtree' => 'Subtree',
    ];
    
    /**
     * Back command name
     *
     * @var string
     */
    protected $backOption = 'phpbin';

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'git';
    
    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setAliases([
            'git:menu'
        ]);
    }
}