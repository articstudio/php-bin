<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 22/01/19
 * Time: 12:30
 */

namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class Remove extends PhpBinCommand {

	use Concerns\HasSubtreesConfig;
	use \Articstudio\PhpBin\Concerns\HasWriteComposer;
	use Concerns\HasSelectBehaviour;

	/**
	 * Command name
	 *
	 * @var string
	 */
	protected static $defaultName = 'git:subtree:remove';

	protected function configure() {
		$this->addArgument( 'package_name', InputArgument::IS_ARRAY, 'Nom del package:' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {

		$repositories        = $this->getSubtrees();
		$input_store         = null;
		$remove_package_name = null;
		$result              = array(
			'skipped'   => [],
			'done'      => [],
			'error'     => [],
			'not_found' => [],
		);

		$package_names = $input->getArgument( 'package_name' ) ?: array();

		if ( empty( $package_names ) ) {

			$option = $this->showPackagesMenu( 'Remove' );

			if ( $option === null ) {
				return 1;
			}

			if ( $option === 'select' ) {
				$message              = 'Select one or multiple packages to would to remove:';
				$choices_repositories = $this->showPackagesChoices( $message, array_keys( $repositories ) );
				$repositories         = $this->getCommonPackages( $repositories, $choices_repositories );
			}

		}

		foreach ( $repositories as $repo_package => $repo_url ) {
			if ( empty( $package_names ) || in_array( $repo_package, $package_names ) ) {
				$remove_package_name = $repo_package;
				$cmd                 = 'git remote rm ' . $repo_package . ' && git rm -r ' . $repo_package . '/  && git commit -m "Removing ' . $repo_package . ' subtree"';
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

		$input_store = $this->showNewPackageQuestions();

		if ( $input_store ) {
			$this->removeSubtreeToComposer( $remove_package_name );
		}

		$this->showResume( $result );

	}

	protected function showNewPackageQuestions( ?bool $force_store = null ) {

		return $force_store === null ? $this->confirmation( 'Remove this package/repository of the Composer config? ' ) : $force_store;

	}
}