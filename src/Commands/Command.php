<?php

declare(strict_types=1);

namespace Articstudio\PhpBin\Commands;

use Articstudio\PhpBin\Ui\Menu;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

abstract class Command extends SymfonyCommand
{

    use \Articstudio\PhpBin\Concerns\HasOutput;
    use \Articstudio\PhpBin\Concerns\HasShell;
    use \Articstudio\PhpBin\Concerns\HasPhpBin;

    /**
     * Create menu
     *
     * @param string $title
     * @param array $options
     *
     * @return Menu
     */
    public function menu(string $title, array $options): Menu
    {
        return new Menu($title, $options);
    }

    public function showMenu(string $title, array $menu_options)
    {
        return $this->menu($title, $menu_options)->open() ?? null;
    }

    public function showChoices(string $message, array $packages)
    {
        return $this->choiceQuestion($message, $packages);
    }

    public function question(
        string $txt,
        $default = null,
        ?OutputInterface $output = null,
        ?InputInterface $input = null
    ) {
        $question_helper = $this->getHelper('question');
        $question = new Question($txt, $default);

        return $question_helper->ask(
            ($input ?? new ArrayInput([])),
            ($output ?? new ConsoleOutput()),
            $question
        );
    }

    public function confirmation(
        string $txt,
        $default = false,
        ?OutputInterface $output = null,
        ?InputInterface $input = null
    ) {
        $question_helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($txt, $default);

        return ! ! $question_helper->ask(
            ($input ?? new ArrayInput([])),
            ($output ?? new ConsoleOutput()),
            $question
        );
    }

    public function choiceQuestion(
        string $txt,
        array $options,
        ?OutputInterface $output = null,
        ?InputInterface $input = null
    ) {
        $question_helper = $this->getHelper('question');
        $question = new ChoiceQuestion($txt, $options);
        $question->setMultiselect(true);

        return $question_helper->ask(
            ($input ?? new ArrayInput([])),
            ($output ?? new ConsoleOutput()),
            $question
        );
    }

    public function selectPackageMenu(string $title, array $menu_options)
    {
        $menu = $this->menu($title, $menu_options);
        $menu->addLineBreak();
        $menu->addOption('back', 'Back');

        return $menu->open();
    }

    /**
     * Call command by name
     *
     * @param string $name
     * @param array $arguments
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function callCommandByName(string $name, ?array $arguments, OutputInterface $output)
    {
        if (! $this->getApplication()->has($name)) {
            throw new \Articstudio\PhpBin\PhpBinException("Command `{$name}` not found.");
        }
        $command = $this->getApplication()->get($name);
        $input = new ArrayInput($arguments ?? []);

        return $command->run($input, $output);
    }
}
