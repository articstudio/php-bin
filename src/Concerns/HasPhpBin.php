<?php declare(strict_types = 1);

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
     * Inject the PHPBin Application instance
     *
     * @param Application $phpbin
     * @return void
     */
    public function injectPhpBin(Application $phpbin): void
    {
        $this->phpbin = $phpbin;
    }
}
