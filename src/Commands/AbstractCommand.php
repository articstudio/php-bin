<?php
namespace Articstudio\PhpBin\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Articstudio\PhpBin\Ui\Menu;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Articstudio\PhpBin\PhpBinException;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

abstract class AbstractCommand extends SymfonyCommand
{

    use \Articstudio\PhpBin\Concerns\HasOutput;
    use \Articstudio\PhpBin\Concerns\HasShell;
    use \Articstudio\PhpBin\Concerns\HasPhpBin;

    /**
     * Create menu
     *
     * @param string $title
     * @param array $options
     * @return Menu
     */
    public function menu(string $title, array $options): Menu
    {
        return new Menu($title, $options);
    }

    public function question(string $question, $default = null, ?OutputInterface $output = null, ?InputInterface $input = null)
    {
        $question_helper = $this->getHelper('question');
        $question = new Question($question, $default);
        return $question_helper->ask(
                ($input ?? new ArrayInput([])),
                ($output ?? new ConsoleOutput),
                $question
        );
    }

    public function confirmation(string $question, $default = false, ?OutputInterface $output = null, ?InputInterface $input = null)
    {
        $question_helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($question, $default);
        return !!$question_helper->ask(
                ($input ?? new ArrayInput([])),
                ($output ?? new ConsoleOutput),
                $question
        );
    }

    /**
     * Call command by name
     *
     * @param string $name
     * @param array $arguments
     * @param OutputInterface $output
     * @return int
     */
    protected function callCommandByName(string $name, ?array $arguments, OutputInterface $output)
    {
        if (!$this->getApplication()->has($name)) {
            throw new PhpBinException("Command `{$name}` not found.");
        }
        $command = $this->getApplication()->get($name);
        $input = new ArrayInput($arguments ?? []);
        return $command->run($input, $output);
    }
}
