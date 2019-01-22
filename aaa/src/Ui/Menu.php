<?php
namespace Articstudio\PhpBin\Ui;

use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use Articstudio\PhpBin\Ui\MenuOption;

class Menu extends CliMenuBuilder
{

    /**
     * Line break
     *
     * @var string
     */
    protected static $lineBreak = ' ';

    /**
     * Title separator
     *
     * @var string
     */
    protected static $titleSeparator = '-';

    /**
     * Menu client
     *
     * @var CliMenu
     */
    protected $client;

    /**
     * Menu constructor
     *
     * @param string $title
     * @param array $options
     */
    public function __construct($title, array $options)
    {
        parent::__construct();
        $this->addLineBreak(static::$lineBreak)
            ->setTitleSeparator(static::$titleSeparator)
            ->setMarginAuto()
            ->setTitle($title)
            ->addOptions($options);
    }

    /**
     * Add menu options
     *
     * @param array $options
     * @return \self
     */
    public function addOptions(array $options): self
    {

        array_walk($options, function (string $label, $value) {
            $this->addOption($value, $label);
        });
        return $this;
    }

    /**
     * Add option
     *
     * @param type $value
     * @param string $label
     * @param callable|null $callback
     * @param bool $showItemExtra
     * @param bool $disabled
     * @return \self
     */
    public function addOption(
        $value,
        string $label,
        ?callable $callback = null,
        bool $showItemExtra = false,
        bool $disabled = false
    ): self {
        return $this->addMenuItem(
            new MenuOption(
                $this,
                $value,
                $label,
                $callback,
                $showItemExtra,
                $disabled
            )
        );
    }

    /**
     * Open menu and return the result
     *
     * @return mixed
     */
    public function open()
    {
        $this->client = $this->build();
        $this->client->open();
        return $this->result ?? null;
    }

    /**
     * Set the result
     *
     * @param mixed $result
     * @return \self
     */
    public function setResult($result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get the menu client instance
     *
     * @return CliMenu
     */
    public function getMenu(): CliMenu
    {
        return $this->client;
    }
}
