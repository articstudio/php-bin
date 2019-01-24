<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 24/01/19
 * Time: 13:22
 */

namespace Articstudio\PhpBin\Commands\Composer\Concerns;


trait HasComposerBehaviour {

	protected function checkParametersPackages($module_dir) {
		$modules = [];
		if ( $module_dir === null ) {
			$option = $this->showPackagesMenu();
			if ( $option === null ) {
				return 1;
			}
			if ( $option === 'select' ) {
				$modules[ $this->showNewPackageQuestions() ] = '';
			}
			if ( $option === 'all' ) {
				$modules = $this->getSubtrees();
			}
		}else {
			$modules[ $module_dir ] = '';
		}
		return $modules;
	}

	protected function getComposerJson( $dirname ) {
		$command = 'find ' . $dirname . ' -type f -name "composer.json"';
		list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell( $command, false );
		$return = array_filter( explode( "\n", $output ), function ( $value ) {
			return $value !== '';
		} );

		return ( $exit_code === 0 ) ? $return : [];
	}

	protected function showPackagesMenu() {
		$menu_options = [
			'select' => 'Get a single module',
			'all'    => 'Get all modules'
		];
		$menu         = $this->menu( 'Modules', $menu_options );

		return $menu->open() ?? null;
	}

}