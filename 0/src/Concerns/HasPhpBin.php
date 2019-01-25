<?php
namespace Articstudio\PhpBin\Concerns;

use Articstudio\PhpBin\Application;

trait HasPhpBin
{

    /**
     * PHPBin Application instance
     *
     * @var \Articstudio\PhpBin\Application
     */
    protected $phpbin;

    /**
     * Set the PHPBin Application instance
     * @param Application $phpbin
     * @return void
     */
    public function setPhpBin(Application $phpbin): void
    {
        $this->phpbin = $phpbin;
    }
}
