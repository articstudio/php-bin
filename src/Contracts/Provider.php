<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Contracts;

interface Provider
{

    /**
     * Register provider
     *
     * @return \self
     */
    public function register(): self;
}
