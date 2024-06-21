<?php

namespace CristianG\PimpleConsole;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputOption;


/**
 * Register the console application with the container.
 *
 * @author Cristian G Danasel
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @param  Container  $pimple
     * @param $di
     * @return void
     */
    public function register(Container $pimple): void
    {
        $pimple['console.name'] = 'Console';
        $pimple['console.version'] = '2.0';
        $pimple['console.classes'] = null;
        $pimple['console.allow_namespace'] = false;
        $pimple['console.di'] = null;

        $pimple['console'] = function (Container $container) {
            $console = new ConsoleApplication($container['console.name'], $container['console.version']);
            if ($container['console.allow_namespace']) {
                $console->getDefinition()->addOption(
                    new InputOption(
                        'namespace',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Specify namespace for the console'
                    )
                );
                $console->setDefaultCommand('list');
                $namespace = array_reduce($_SERVER['argv'], function ($carry, $arg) {
                    return str_starts_with($arg, '--namespace=') ? substr($arg, strlen('--namespace=')) : $carry;
                }, "");

                if (!empty($namespace)) {
                    if (!class_exists($namespace)) {
                        return $console;
                    }
                    $class = new $namespace();
                    if ($container['console.di']) {
                        $class->setDi($container['console.di']);
                    }
                    $console->add($class);
                    return $console;
                }
            }

            if ($container['console.classes']) {
                foreach ($container['console.classes'] as $class) {
                    $class = new $class();
                    if ($container['console.di']) {
                        $class->setDi($container['console.di']);
                    }
                    $console->add($class);
                }
            }
            return $console;
        };
    }
}
