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

	public function addPackageToComposerRequire( array $itemToAdd ) {
		$composer                    = Application::getInstance()->getComposer();
		$composer_file               = $composer['file'];
		$config                      = $composer['data'];
		$subtrees                    = $composer['data']['require'] + $itemToAdd;
		$config['config']['subtree'] = $subtrees;

		$this->writeComposer( $config, $composer_file );
	}

	public function addPackageToComposerRequireDev( array $itemToAdd ) {
		$composer                    = Application::getInstance()->getComposer();
		$composer_file               = $composer['file'];
		$config                      = $composer['data'];
		$subtrees                    = $composer['data']['require-dev'] + $itemToAdd;
		$config['config']['subtree'] = $subtrees;

		$this->writeComposer( $config, $composer_file );
	}

	private function writeComposer( array $config, string $composer_file ) {
		$clean_config = array_map( function ( $value ) {
			return $value === array() ? new \stdClass() : $value;
		}, $config );

		$json    = json_encode( $clean_config, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		$printer = new Printer();

		$printed = $printer->print(
			$json
		);
		$composer_file = fopen( $composer_file, "w" ) or die( "Unable to open file!" );
		fwrite( $composer_file, $printed );
		fclose($composer_file);
	}

}