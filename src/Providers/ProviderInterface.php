<?php declare(strict_types = 1);

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
