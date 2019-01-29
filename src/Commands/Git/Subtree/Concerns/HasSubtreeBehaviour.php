<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree\Concerns;

trait HasSubtreeBehaviour
{

    public function getCommonPackages($repositories, $choices_repositories)
    {
        $res = array();
        foreach ($repositories as $repository => $repository_url) {
            if (in_array($repository, $choices_repositories)) {
                $res[$repository] = $repository_url;
            }
        }

        return $res;
    }

    protected function subtreeExists(string $package_name)
    {
        $cmd = 'find . -type d -wholename "./' . $package_name . '"';
        list($exit_code, $output, $exit_code_txt, $error) = $this->callShell($cmd, false);

        return $output !== "" ? true : false;
    }

    public function showResume(array $result)
    {

        echo "\n" . 'RESUME:' . "\n\n";
        echo 'Skipped packages:' . "\n";
        foreach ($result['skipped'] as $package_name) {
            echo '    - ' . $package_name . "\n";
        }
        echo "\n" . 'Done packages:' . "\n";
        foreach ($result['done'] as $package_name) {
            echo '    - ' . $package_name . "\n";
        }
        echo "\n" . 'Error packages:' . "\n";
        foreach ($result['error'] as $package_name) {
            echo '    - ' . $package_name . "\n";
        }
        echo "\n" . 'Not found packages:' . "\n";
        foreach ($result['not_found'] as $package_name) {
            echo '    - ' . $package_name . "\n";
        }

        echo "\n";
    }
}
