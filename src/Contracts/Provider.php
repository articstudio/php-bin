<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Contracts;

use Articstudio\PhpBin\Application;

interface Provider
{

    /**
     * Register provider
     *
     * @return self
     */
    public function register(): self;

    /**
     * Inject the PHPBin Application instance
     *
     * @param Application $phpbin
     *
     * @return void
     */
    public function injectPhpBin(Application $phpbin): void;
}
