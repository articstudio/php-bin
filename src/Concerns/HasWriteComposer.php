<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 21/01/19
 * Time: 13:22
 */

namespace Articstudio\PhpBin\Concerns;

use Articstudio\PhpBin\Application;
use Localheinz\Json\Printer\Printer;

trait HasWriteComposer {


	public function addSubtreeToComposer( array $itemToAdd ) {
		$composer                    = Application::getInstance()->getComposer();
		$composer_file               = $composer['file'];
		$config                      = $composer['data'];
		$subtrees                    = $composer['data']['config']['subtree'] + $itemToAdd;
		$config['config']['subtree'] = $subtrees;

		$this->writeComposer( $config, $composer_file );
	}

	public function removeSubtreeToComposer( string $itemToRemove ) {
		$composer      = Application::getInstance()->getComposer();
		$composer_file = $composer['file'];
		$config        = $composer['data'];
		unset( $composer['data']['config']['subtree'][ $itemToRemove ] );
		$subtrees                    = $composer['data']['config']['subtree'];
		$config['config']['subtree'] = $subtrees;

		$this->writeComposer( $config, $composer_file );
	}

	public function addPackageToComposerRequire( array $itemToAdd, string $composer_file, $env ) {

		$input_package_name = array_keys($itemToAdd)[0];

		$composer = json_decode( file_get_contents( $composer_file ), true );

		$env = ( $env && ( $env === "d" || $env === "D" ) ) ? 'require-dev' : 'require';

		$packages         = $composer[ $env ] + $itemToAdd;
		$composer[ $env ] = $packages;

		$env = ( $env !== 'require-dev' ) ? 'require-dev' : 'require';

		if ( key_exists($env, $composer) && key_exists( $input_package_name, $composer[ $env ] ) ) {
			unset( $composer[ $env ][ $input_package_name ] );
		}
		$this->writeComposer( $composer, $composer_file );
	}


	private function writeComposer( array $config, string $composer_file ) {
		$clean_config = array_map( function ( $value ) {
			return $value === array() ? new \stdClass() : $value;
		}, $config );


		if ( key_exists('subtree', $clean_config['config']) && empty( $clean_config['config']['subtree'] ) ) {
			$clean_config['config']['subtree'] = new \stdClass();
		}
		$json    = json_encode( $clean_config, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		$printer = new Printer();

		$printed = $printer->print(
			$json
		);
		$composer_file = fopen( $composer_file, "w" ) or die( "Unable to open file!" );
		fwrite( $composer_file, $printed );
		fclose( $composer_file );
	}

}