<?php

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;

use Articstudio\PhpBin\PhpBinException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends PhpBinCommand {

	use \Articstudio\PhpBin\Concerns\HasWriteComposer;
	use Concerns\HasComposerConfig;

	/**
	 * Command name
	 *
	 * @var string
	 */
	protected static $defaultName = 'composer:install';

	protected function configure() {
		$this->addArgument( 'package_name', InputArgument::OPTIONAL, 'Nom del package:' );
		$this->addArgument( 'module_name', InputArgument::OPTIONAL, 'Nom del mòdul:' );
		$this->addArgument( 'envoirment', InputArgument::OPTIONAL, 'Entorn:' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {

		$composer           = $this->getComposerData();
		$input_package_name = $input->getArgument( 'package_name' ) ?: null;
		$input_module_name  = $input->getArgument( 'module_name' ) ?: null;
		$env                = $input->getArgument( 'envoirment' ) ?: null;

		if ( $input_package_name === null || $input_module_name === null || $env === null ) {
			//MENU
			die;
		}

		$version = $this->searchPackageVersion( $input_package_name, $composer );
		$this->requireDevPackage( $version, $input_package_name );

		$partiklo_file = $input_module_name . '/composer.json';

		if ( ! $partiklo_file ) {
			throw new PhpBinException( 'composer.json file not found: ' . $partiklo_file );
		}

		$composer                                = json_decode( file_get_contents( $partiklo_file ), true );
		$env                                     = ( $env && ( $env === 'd' || $env === 'D' ) ) ? 'require-dev' : 'require';
		$composer[ $env ][ $input_package_name ] = $version;

		$env = ( $env !== 'require-dev' ) ? 'require-dev' : 'require';
		if ( isset( $composer[ $env ] ) && key_exists( $input_package_name, $composer[ $env ] ) ) {
			unset( $composer[ $env ][ $input_package_name ] );
		}


	}

	private function requireDevPackage( $version, $input_package_name ) {

		if ( ! $version ) {
			try {
				$command = 'composer require --dev ' . $input_package_name;
				list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell( $command, false );
				if ( $exit_code === 1 ) {
					throw new PhpBinException( "Error installing package: " . $input_package_name );
				}
				$composer = json_decode( file_get_contents( $this->getComposerFile() ), true );
				$version  = $this->searchPackageVersion( $input_package_name, $composer );
			} catch ( PhpBinException $exception ) {
				echo 'Caught exception package: ', $exception->getMessage() . "\n";
				exit( 1 );
			}
		}

		if ( ! $version ) {
			try {
				throw new PhpBinException( "Package not found: " . $input_package_name );
			} catch ( PhpBinException $exception ) {
				echo "Caught exception package: " . $exception->getMessage() . "\n";
				exit( 1 );
			}
		}

	}

	private function searchPackageVersion( $search_package, $package_json ) {
		$result = false;

		if ( key_exists( $search_package, $package_json['require'] ) ) {
			$result = $package_json['require'][ $search_package ];
		} elseif ( key_exists( $search_package, $package_json['require-dev'] ) ) {
			$result = $package_json['require-dev'][ $search_package ];
		}

		return $result;

	}

	private function showHelp() {
		printf( "Usage: \n" );
		printf( "\t composer install-dev PACKAGE-TO-INSTALL PARTIKLO-NAME [-d|-D] \n" );
		printf( "\t\t PACKAGE-TO-INSTALL: name of package will install to partiklo \n" );
		printf( "\t\t PARTIKLO-NAME: name of the partiklo where the package will be installed, this name appears into respective package.json \n" );
		printf( "\t\t [-d|-D]: Put the package and version into devDependencies scope \n" );

		printf( "\n " );
		printf( "\t Example: \n" );
		printf( "\t\t composer install-dev psr/log partiklo/contracts -d\n" );
		printf( "\t\tcomposer install-dev psr/log partiklo/contracts\n" );
	}
}
