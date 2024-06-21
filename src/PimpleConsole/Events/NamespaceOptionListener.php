<?php

namespace CristianG\PimpleConsole\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class NamespaceOptionListener implements EventSubscriberInterface
{
    /**
     * @var
     */
    private $container;

    /**
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            \Symfony\Component\Console\ConsoleEvents::COMMAND => ['onBeforeCommand', -300],
        ];
    }

    /**
     * @param  ConsoleCommandEvent  $event
     * @return void
     */
    public function onBeforeCommand(ConsoleCommandEvent $event): void
    {
        $input = $event->getInput();
        $output = $event->getOutput();

        // Retrieve the value of the --namespace option
        $namespace = $input->getOption('namespace');

        if ($namespace) {
            $output->writeln("Namespace specified: $namespace");
            // Store or process the namespace as needed
            $this->container['current_namespace'] = $namespace;
        } else {
            $output->writeln("No namespace specified");
            // Set a default or handle absence of namespace
            $this->container['current_namespace'] = 'default_namespace';
        }
    }
}
