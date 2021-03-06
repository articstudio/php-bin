<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\MenuCommand as PhpBinMenuCommand;

class Menu extends PhpBinMenuCommand
{

    /**
     * Menu title
     *
     * @var string
     */
    protected $menuTitle = 'Composer';

    /**
     * Menu options
     *
     * @var array
     */
    protected $menuOptions = [
        'composer:install' => 'Install package',
        'composer:update-versions' => 'Update versions',
        'composer:normalize' => 'Normalize',
        'composer:dev-packages' => 'Load packages',
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
    protected static $defaultName = 'composer';

    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setAliases([
            'composer:menu',
        ]);
    }
}
