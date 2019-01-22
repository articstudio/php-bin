<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 22/01/19
 * Time: 11:17
 */

namespace Articstudio\PhpBin\Commands\Git\Subtree\Concerns;


trait HasSelectBehaviour {


	public function showPackagesMenu(string $command) {
		$menu_options = [
			'select' => 'Select subtrees',
			'all'    => $command . ' all subtrees'
		];
		$menu         = $this->menu( 'Subtree packages', $menu_options );

		return $menu->open();
	}

	public function showPackagesChoices( string $message, array $packages ) {
		return $this->choiceQuestion( $message, $packages );
	}

	public function getCommonPackages( $repositories, $choices_repositories ) {
		$res = array();
		foreach ( $repositories as $repository => $repository_url ) {
			if ( in_array( $repository, $choices_repositories ) ) {
				$res[ $repository ] = $repository_url;
			}
		}

		return $res;
	}

	public function showResume( array $result ) {

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