<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 24/01/19
 * Time: 9:29
 */

namespace Articstudio\PhpBin\Commands\Composer;

use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;

use Articstudio\PhpBin\PhpBinException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetDevPackages extends PhpBinCommand {

	use \Articstudio\PhpBin\Concerns\HasWriteComposer;
	use Concerns\HasComposerConfig;
	use \Articstudio\PhpBin\Commands\Git\Subtree\Concerns\HasSubtreesConfig;

	protected $composer;

	/**
	 * Command name
	 *
	 * @var string
	 */
	protected static $defaultName = 'composer:dev-packages';

	protected function configure() {
		$this->addArgument( 'module_name', InputArgument::OPTIONAL, 'Nom del mòdul:' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->composer         = $this->getComposerData();
		$module_dir             = $input->getArgument( 'module_name' ) ?: null;
		$modules                = [];
		if ( $module_dir === null ) {
			//MENU
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

		$requires_dev = array(
			'require-dev' => array()
		);

		foreach ( $modules as $module_name => $module_url ) {
			$this->composer = array_merge( $this->composer, $requires_dev );

			array_map( function ( $name ) {
				$this->mergeDependencies( $name );
			}, $this->getComposerJson( $module_name ) );
		}


		$this->writeComposer( $this->composer, $this->getComposerFile() );
	}

	protected function addDependencies( $dependencies, $fname ) {
		if ( ! $dependencies ) {
			return;
		}
		foreach ( $dependencies as $dependency => $version ) {
			if ( ! key_exists( $dependency, $this->composer['require'] ) && ! key_exists( $dependency, $this->composer['require-dev'] ) ) {
				$this->composer['require-dev'][ $dependency ] = $version;
				printf( "  + %s@%s \n", $dependency, $version );
			} else {
				if ( key_exists( $dependency, $this->composer['require-dev'] ) && $this->composer['require-dev'][ $dependency ] === $version
				     || key_exists( $dependency, $this->composer['require'] ) && $this->composer['require'][ $dependency ] === $version ) {
					printf( "  = %s@%s \n", $dependency, $version );
				} else {
					printf( "  ! %s@%s \n", $dependency, $version );
				}
			}
		}
	}

	private function getComposerJson( $dirname ) {
		$command = 'find ' . $dirname . ' -type f -name "composer.json"';
		list( $exit_code, $output, $exit_code_txt, $error ) = $this->callShell( $command, false );
		$return = array_filter( explode( "\n", $output ), function ( $value ) {
			return $value !== '';
		} );

		return ( $exit_code === 0 ) ? $return : [];
	}

	private function mergeDependencies( $fname ) {
		printf( "%s: \n", $fname );
		$data = json_decode( file_get_contents( $fname ), true );
		if ( key_exists( 'require', $data ) ) {
			$this->addDependencies( $data['require'], $fname );
		}
		if ( key_exists( 'require-dev', $data ) ) {
			$this->addDependencies( $data['require-dev'], $fname );
		}
	}

	public function showPackagesMenu() {
		$menu_options = [
			'select' => 'Get a single module',
			'all'    => 'Get all modules'
		];
		$menu         = $this->menu( 'Modules', $menu_options );

		return $menu->open() ?? null;
	}

	protected function showNewPackageQuestions() {
		return $this->question( 'Please enter the name of the module where you want to get the require/require-dev packages: ' );
	}

}