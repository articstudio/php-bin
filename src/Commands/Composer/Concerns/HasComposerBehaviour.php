<?php declare(strict_types = 1);

namespace Articstudio\PhpBin\Commands\Composer\Concerns;

trait HasComposerBehaviour
{

    protected function getComposerJson($dirname)
    {
        $command = 'find ' . $dirname . ' -type f -name "composer.json"';
        [$exit_code, $output, , ] = $this->callShell($command, false);
        $return = array_filter(explode("\n", $output), function ($value) {
            return $value !== '';
        });

        return $exit_code === 0 ? $return : [];
    }

    protected function getModulesByOption($option)
    {
        $modules = [];
        if ($option === 'all') {
            $modules = array_keys($this->getSubtrees());
        } elseif ($option === 'root') {
            $modules[] = $this->getComposerFile();
        } elseif (is_int($option)) {
            $modules[] = array_keys($this->getSubtrees())[$option];
        }

        return $modules;
    }
}
