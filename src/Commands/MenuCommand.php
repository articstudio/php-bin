<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands;

use Articstudio\PhpBin\Ui\Menu;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class MenuCommand extends Command
{

    /**
     * Menu title
     *
     * @var string
     */
    protected $menuTitle = '';

    /**
     * Menu options
     *
     * @var array
     */
    protected $menuOptions = [];

    /**
     * Show exit option
     *
     * @var bool
     */
    protected $showExitOption = true;

    /**
     * Back command name
     *
     * @var string
     */
    protected $backOption;

    /**
     * Is menu title prepared
     *
     * @var bool
     */
    private $menuTitlePrepared = false;

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareMenuTitle();
        $name = $this->menu($this->menuTitle, $this->menuOptions)->open();
        if (! $name) {
            return $this->exit($output);
        }
        return $this->callCommandByName($name, [], $output);
    }

    private function prepareMenuTitle(): void
    {
        if ($this->menuTitlePrepared) {
            return;
        }
        $this->menuTitle = $this->menuTitle ?? '';
        $this->menuTitle .= (! $this->menuTitle ? '' : ' - ') . "PHPBIN v{$this->phpbin->getVersion()}";
        $this->menuTitlePrepared = true;
    }

    /**
     * Create menu (Override)
     *
     * @param string $title
     * @param array $options
     *
     * @return Menu
     */
    public function menu(string $title, array $options): Menu
    {
        $menu = parent::menu($title, $options);

        if ($this->backOption || $this->showExitOption) {
            $menu->addLineBreak();
        }

        if ($this->backOption) {
            $menu->addOption($this->backOption, 'Back');
        }

        if (! $this->showExitOption) {
            $menu->disableDefaultItems();
        }

        return $menu;
    }

    /**
     * Extend menu options
     *
     * @param array $options
     *
     * @return void
     */
    public function extendOptions(array $options): void
    {
        $this->menuOptions = array_merge($this->menuOptions, $options);
    }
}
