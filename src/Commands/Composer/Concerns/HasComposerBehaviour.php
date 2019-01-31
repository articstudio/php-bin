<?php

namespace Articstudio\PhpBin\Commands\Composer\Concerns;

trait HasComposerBehaviour
{

    protected function getComposerJson($dirname)
    {
        $command = 'find ' . $dirname . ' -type f -name "composer.json"';
        list($exit_code, $output, $exit_code_txt, $error) = $this->callShell($command, false);
        $return = array_filter(explode("\n", $output), function ($value) {
            return $value !== '';
        });

        return ($exit_code === 0) ? $return : [];
    }

    protected function getModulesByOption($option)
    {
        $modules = [];
        if ($option === 'all') {
            $modules = array_keys($this->getSubtrees());
        } elseif ($option === 'root') {
            $modules[] = $this->getComposerFile();
        } else {
            $modules = is_int($option) ? array_keys($this->getSubtrees())[$option] : [];
        }

        return $modules;
    }
}
