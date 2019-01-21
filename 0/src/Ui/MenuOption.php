<?php
namespace Articstudio\PhpBin\Ui;

use Articstudio\PhpBin\Ui\Menu;
use PhpSchool\CliMenu\MenuItem\SelectableItem;

class MenuOption extends SelectableItem
{

    /**
     * Option value
     *
     * @var mixed
     */
    private $value;

    /**
     * Menu builder instance
     *
     * @var \Articstudio\PhpBin\Ui\Menu
     */
    private $builder;

    /**
     * Menu option constructor
     *
     * @param mixed $value
     * @param string $label
     * @param callable $callback
     * @param bool $showItemExtra
     * @param bool $disabled
     */
    public function __construct(
        Menu $builder,
        $value,
        string $label,
        ?callable $callback,
        bool $showItemExtra = false,
        bool $disabled = false
    ) {
        parent::__construct(
            $label,
            ($callback ?? static::getDefaultCallback($builder)),
            $showItemExtra,
            $disabled
        );
        $this->builder = $builder;
        $this->value = $value;
    }

    /**
     * Get the value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public static function getDefaultCallback(Menu $builder)
    {
        return function () use ($builder) {
            $builder->setResult(
                $builder->getMenu()->getSelectedItem()->getValue()
            );
            $builder->getMenu()->close();
        };
    }
}
