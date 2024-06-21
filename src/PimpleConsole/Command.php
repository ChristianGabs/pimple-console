<?php

namespace CristianG\PimpleConsole;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;


abstract class Command extends SymfonyCommand
{
    /**
     * The input interface implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected InputInterface $input;

    /**
     * The output interface implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected OutputInterface $output;

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        return parent::run($input, $output);
    }

    /**
     * @param $string
     *
     * @return void
     */
    public function line($string): void
    {
        $this->output->writeln($string);
    }

    /**
     * @param $string
     * @return void
     */
    public function info($string): void
    {
        $this->output->writeln("<info>$string</info>");
    }

    /**
     * @param $string
     *
     * @return void
     */
    public function comment($string): void
    {
        $this->output->writeln("<comment>$string</comment>");
    }

    /**
     * @param $string
     *
     * @return void
     */
    public function question($string): void
    {
        $this->output->writeln("<question>$string</question>");
    }

    /**
     * @param $string
     *
     * @return void
     */
    public function error($string): void
    {
        $this->output->writeln("<error>$string</error>");
    }

    /**
     * Confirm a question.
     * @param $question
     * @param $default
     * @param $trueAnswerRegex
     *
     * @return bool
     */
    public function confirm($question, $default = false, $trueAnswerRegex = '/^y/i'): bool
    {
        $question = new ConfirmationQuestion(
            "<question>{$question}</question> ",
            $default,
            $trueAnswerRegex
        );

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    /**
     * Asks a question to the user.
     * 
     * @param $question
     * @param $default
     * 
     * @return mixed The user answer
     */
    public function ask($question, $default = null): string
    {
        $question = new Question("<question>$question</question> ", $default);
        
        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    /**
     * Give the user a single choice from an array of answers.
     * @param $question
     * @param  array  $choices
     * @param $default
     * @param $attempts
     * @param $multiple
     *
     * @return mixed
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null): mixed
    {
        $question = new ChoiceQuestion("<question>$question</question> ", $choices, $default);
        $question->setMultiselect($multiple)->setMaxAttempts($attempts);

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }

    /**
     * @param  array  $headers
     * @param  array  $rows
     * @param $style
     * @return void
     */
    public function table(array $headers, array $rows, $style = 'default'): void
    {
        $table = new Table($this->output);

        $table->setHeaders($headers)->setStyle($style);
        if (is_array(reset($rows))) {
            foreach ($rows as $row) {
                $table->addRow($row);
            }
        } else {
            $table->addRow($rows);
        }
        $table->render();
    }
}
