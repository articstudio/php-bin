<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 24/01/19
 * Time: 13:22
 */

namespace Articstudio\PhpBin\Commands\Composer\Concerns;


trait HasComposerBehaviour {

	protected function getComposerJson( $dirname ) {
		$command = 'find ' . $dirname . ' -type f -name "composer.json"';
		list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell( $command, false );
		$return = array_filter( explode( "\n", $output ), function ( $value ) {
			return $value !== '';
		} );

		return ( $exit_code === 0 ) ? $return : [];
	}

	protected function getModulesByOption( $option ) {
		$modules = [];
		if ( $option === 'select' ) {
			$modules = $this->showPackagesChoices( "Select a module to normalize composer: ", array_keys( $this->getSubtrees() ) );
		} else if ( $option === 'all' ) {
			$modules = array_keys( $this->getSubtrees() );
		} else if ( $option === 'root' ) {
			$modules[] = $this->getComposerFile();
		}

		return $modules;
	}

}