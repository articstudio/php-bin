<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Push extends PhpBinCommand {

	use Concerns\HasSubtreesConfig;

	/**
	 * Command name
	 *
	 * @var string
	 */
	protected static $defaultName = 'git:subtree:push';

	protected function configure() {
		$this->addArgument( 'package_name', InputArgument::IS_ARRAY, 'Nom del package:' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$repositories = $this->getSubtrees();
		$result       = array(
			'skipped'   => [],
			'done'      => [],
			'error'     => [],
			'not_found' => [],
		);

		$package_names = $input->getArgument( 'package_name' ) ?: array();

		if ( empty( $package_names ) ) {

			$option = $this->showPackagesMenu();
			if ( $option === 'select' ) {
				$choices_repositories = $this->showPackagesChoices( array_keys( $repositories ) );
				$repositories = $this->getCommonPackages( $repositories, $choices_repositories );
			}

		}

		foreach ( $repositories as $repo_package => $repo_url ) {
			if ( empty( $package_names ) || in_array( $repo_package, $package_names ) ) {
				$cmd = 'git subtree push --prefix=' . $repo_package . '/ ' . $repo_url . ' master';
				list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell( $cmd, false );
				$key              = $exit_code === 0 ? 'done' : 'error';
				$result[ $key ][] = $repo_package;
				continue;
			}
			$result['skipped'][] = $repo_package;
		}

		foreach ( $package_names as $package_name ) {
			if ( ! isset( $repositories[ $package_name ] ) ) {
				$result['not_found'][] = $package_name;
			}
		}

		$this->showResumePush( $result );

	}

	protected function showPackagesMenu() {
		$menu_options = [
			'select' => 'Select subtrees',
			'all'    => 'Push all subtrees'
		];
		$menu         = $this->menu( 'Subtree packages', $menu_options );

		return $menu->open();
	}

	protected function showPackagesChoices( array $packages ) {
		return $this->choiceQuestion( 'Select one or multiple packages to would to push:', $packages );
	}

	protected function getCommonPackages( $repositories, $choices_repositories ) {
		$res = array();
		foreach ( $repositories as $repository => $repository_url ) {
			if ( in_array( $repository, $choices_repositories ) ) {
				$res[ $repository ] = $repository_url;
			}
		}

		return $res;
	}

	protected function showResumePush( array $result ) {

		echo "\n" . 'RESUME:' . "\n\n";
		echo 'Skipped packages:' . "\n";
		foreach ( $result['skipped'] as $package_name ) {
			echo '    - ' . $package_name . "\n";
		}
		echo "\n" . 'Done packages:' . "\n";
		foreach ( $result['done'] as $package_name ) {
			echo '    - ' . $package_name . "\n";
		}
		echo "\n" . 'Error packages:' . "\n";
		foreach ( $result['error'] as $package_name ) {
			echo '    - ' . $package_name . "\n";
		}
		echo "\n" . 'Not found packages:' . "\n";
		foreach ( $result['not_found'] as $package_name ) {
			echo '    - ' . $package_name . "\n";
		}

		echo "\n";
	}
}
