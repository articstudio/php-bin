<?php declare(strict_types = 1);

namespace Articstudio\PhpBin\Commands\Git\Subtree\Concerns;

trait HasSubtreeBehaviour
{

    protected function subtreeExists(string $package_name)
    {
        $cmd = 'find . -type d -wholename "./' . $package_name . '"';
        [, $output, , ] = $this->callShell($cmd, false);

        return $output !== "" ? true : false;
    }

    public function showResume(array $result, $io)
    {

        $io->title("RESUME: ");
        $io->newLine();
        $io->section('Skipped packages:');
        foreach ($result['skipped'] as $package_name) {
            $io->writeln('    - ' . $package_name);
        }
        $io->section('Done packages:');
        foreach ($result['done'] as $package_name) {
            $io->writeln('    - ' . $package_name);
        }
        $io->section('Error packages:');
        foreach ($result['error'] as $package_name) {
            $io->writeln('    - ' . $package_name);
        }
        $io->section('Not found packages:');
        foreach ($result['not_found'] as $package_name) {
            $io->writeln('    - ' . $package_name);
        }

        $io->newLine();
    }
}
