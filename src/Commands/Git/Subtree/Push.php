<?php

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Push extends PhpBinCommand {

	use Concerns\HasSubtreesConfig;
	use Concerns\HasSelectBehaviour;

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

			$option = $this->showPackagesMenu('Push');
			if ( $option === 'select' ) {
				$message = 'Select one or multiple packages to would to push:';
				$choices_repositories = $this->showPackagesChoices( $message, array_keys( $repositories ) );
				$repositories         = $this->getCommonPackages( $repositories, $choices_repositories );
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

		$this->showResume( $result );

	}
}
