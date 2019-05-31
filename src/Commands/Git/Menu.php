<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands\Git;

use Articstudio\PhpBin\Commands\MenuCommand as PhpBinMenuCommand;

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
        'git:subtree:add' => 'Subtree Add',
        'git:subtree:push' => 'Subtree Push',
        'git:subtree:pull' => 'Subtree Pull',
        'git:subtree:remove' => 'Subtree Remove',
        'git:subtree:check' => 'Subtree Check',
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
            'git:menu',
        ]);
    }
}
