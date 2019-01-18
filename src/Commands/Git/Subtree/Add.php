<?php
namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Application;
use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Articstudio\PhpBin\PhpBinException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends PhpBinCommand
{
	/**
	 * Command name
	 *
	 * @var string
	 */
	protected static $defaultName = 'git:subtree:add';

	protected function configure()
	{
		$this->addArgument('package_name', InputArgument::REQUIRED, 'Nom del package:');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$composer = Application::getInstance()->getComposer();
		$io = $this->getStyle($output, $input);
		$repositories = $composer['data']['config']['subtree'];
		$package_name = $input->getArgument('package_name') ?: '';

		if(!isset($repositories[$package_name])) {
			throw new PhpBinException('Package '. $package_name . ' configuration not found');
		}
		$cmd = 'git subtree add --prefix=' . $package_name . '/ ' . $repositories[$package_name] . ' master';

		$called_shell = $this->callShell($cmd);

		if(!is_array($called_shell)) {
			throw new PhpBinException('Error adding the  package ' . $package_name . ' subtree from ' . $repositories[$package_name] . '');
		}

		$io->writeln('Package "' . $package_name . '" subtree from "' . $repositories[$package_name] . '" added CORRECTLY!');

	}

}