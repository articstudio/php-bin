<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Concerns;

trait HasComposer {

    /**
     * Composer directory
     *
     * @var string
     */
    private $composer_dir;

    /**
     * Composer file
     *
     * @var string
     */
    private $composer_file;

    /**
     * Composer settings
     *
     * @var array
     */
    private $composer;

    /**
     * Settings
     *
     * @var array
     */
    private $composer_settings;

    /**
     * Discover composer.json
     *
     * @return self
     */
    protected function discoverComposer(): self
    {
        $this->composer_dir = dirname(
            dirname(PHPBIN_COMPOSER_AUTOLOAD)
        );
        $this->composer_file = realpath($this->composer_dir . '/composer.json');
        if (! is_readable($this->composer_file)) {
            throw new \Articstudio\PhpBin\PhpBinException("composer.json not readable at `{$this->composer_dir}`");
        }
        $this->composer = [
            'directory' => $this->composer_dir,
            'file' => $this->composer_file,
            'data' => json_decode(file_get_contents($this->composer_file), true),
        ];
        return $this;
    }

    /**
     * Parse settings
     *
     * @return self
     */
    protected function parseComposerSettings(): self
    {
        $config = $this->composer['data']['config'] ?? [];
        $this->composer_settings = $config['phpbin'] ?? [];
        return $this;
    }

    /**
     * Get composer settings
     *
     * @return array
     */
    public function getComposer()
    {
        return $this->composer;
    }
}
