<?php
namespace Articstudio\PhpBin\Commands\Git\Subtree;

use Articstudio\PhpBin\Application;
use Articstudio\PhpBin\Commands\AbstractCommand as PhpBinCommand;
use Articstudio\PhpBin\PhpBinException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Add extends PhpBinCommand
{

	protected $menuOptions = [ ];
	/**
	 * Command name
	 *
	 * @var string
	 */
	protected static $defaultName = 'git:subtree:add';

	protected function configure()
	{
		$this->addArgument('package_name', InputArgument::OPTIONAL, 'Nom del package:');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$composer = Application::getInstance()->getComposer();
		$io = $this->getStyle($output, $input);
		$repositories = $composer['data']['config']['subtree'];
		$package_name = $input->getArgument('package_name') ?: null;

		if($package_name !== null){
			//Console
			if(!isset($repositories[$package_name])) {
				throw new PhpBinException('Package '. $package_name . ' configuration not found');
			}
			$cmd = 'git subtree add --prefix=' . $package_name . '/ ' . $repositories[$package_name] . ' master';

			$called_shell = $this->callShell($cmd);

			if(!is_array($called_shell)) {
				throw new PhpBinException('Error adding the  package ' . $package_name . ' subtree from ' . $repositories[$package_name] . '');
			}

			$io->writeln('Package "' . $package_name . '" subtree from "' . $repositories[$package_name] . '" added CORRECTLY!');
		}else {
			//Menu
			$io->writeln("Console");
			$other_option = "Select other package: ";
			$helper = $this->getHelper('question');
			$subtrees = array_keys($repositories);
			array_push($subtrees, $other_option);
			$question = new ChoiceQuestion(
				'Please select a package for add to subtree',
				$subtrees,
				0
			);
			$question->setErrorMessage('Package %s is invalid.');
			$package_name = $helper->ask($input, $output, $question);

			$new_question = new Question('Please enter the name of the package: ', '');
			if($package_name === $other_option ) {
				$package_name = $helper->ask($input, $output, $new_question);
			}

			$io->writeln($package_name);
		}

	}


}