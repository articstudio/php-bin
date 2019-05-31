<?php

namespace Articstudio\PhpBin\Providers;

interface ProviderInterface
{

    /**
     * Register provider
     *
     * @return \self
     */
    public function register(): self;
}
