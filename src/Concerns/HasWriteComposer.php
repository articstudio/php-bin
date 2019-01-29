<?php

namespace Articstudio\PhpBin\Concerns;

use Articstudio\PhpBin\Application;
use Localheinz\Json\Printer\Printer;

trait HasWriteComposer
{


    public function addSubtreeToComposer(array $itemToAdd)
    {
        $composer      = Application::getInstance()->getComposer();
        $composer_file = $composer['file'];
        $config        = $composer['data'];
        if ( ! key_exists('subtree', $config['config'])) {
            $config['config']['subtree'] = [];
        }
        $subtrees                    = array_merge($config['config']['subtree'], $itemToAdd);
        $config['config']['subtree'] = $subtrees;

        $this->writeComposer($config, $composer_file);
    }

    public function removeSubtreeToComposer(array $itemsToRemove)
    {
        $composer         = Application::getInstance()->getComposer();
        $composer_file    = $composer['file'];
        $config           = $composer['data'];
        if ( ! key_exists('subtree', $config['config'])) {
            $config['config']['subtree'] = [];
        }
        $current_subtrees = $config['config']['subtree'];

        foreach ($current_subtrees as $current_subtree => $current_subtree_url) {
            if (in_array($current_subtree, $itemsToRemove)) {
                unset($current_subtrees[$current_subtree]);
            }
        }

        $config['config']['subtree'] = $current_subtrees;

        $this->writeComposer($config, $composer_file);
    }

    public function addPackageToComposerRequire(array $itemToAdd, string $composer_file, $env)
    {

        $input_package_name = array_keys($itemToAdd)[0];

        $composer = json_decode(file_get_contents($composer_file), true);

        $env = ($env && ($env === "d" || $env === "D")) ? 'require-dev' : 'require';

        $packages       = $composer[$env] + $itemToAdd;
        $composer[$env] = $packages;

        $env = ($env !== 'require-dev') ? 'require-dev' : 'require';

        if (key_exists($env, $composer) && key_exists($input_package_name, $composer[$env])) {
            unset($composer[$env][$input_package_name]);
        }
        $this->writeComposer($composer, $composer_file);
    }

    private function writeComposer(array $config, string $composer_file)
    {
        $clean_config = array_map(function ($value) {
            return $value === array() ? new \stdClass() : $value;
        }, $config);

        if (key_exists('subtree', $clean_config['config']) && empty($clean_config['config']['subtree'])) {
            $clean_config['config']['subtree'] = new \stdClass();
        }
        $json    = json_encode($clean_config, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $printer = new Printer();

        $printed = $printer->print(
            $json
        );
        $composer_file = fopen($composer_file, "w") or die("Unable to open file!");
        fwrite($composer_file, $printed);
        fclose($composer_file);
    }
}
