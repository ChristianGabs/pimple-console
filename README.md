# Pimple Console ServiceProvider

## Installation
Add the console provider to your ```composer.json``` using the command line.
```
composer require cristiang/pimple-console
```

## Configuration
```php
use Pimple\Container;
use CristianG\PimpleConsole\ServiceProvider;

$container = new Container();
$container->register(new ServiceProvider(), [
    /**
     * Set the console application name. Defaults to 'Console'
     * @param  string
     */
    'console.name'    => 'Your App Console Commands',
    /**
     * Set the console application version. Defaults to '2.0'
     * @param  string
     */
    'console.version' => "2.0.0",
    /**
     * Set console application list
     * @param  array
     */
    'console.classes' => [
        "\App\Console\Version",
        "\App\Console\Sync",
    ],
    /**
     * Set namespace command --namespace="\Namespace\Run" to be provided on command
     * !Note : console.classes will be ignore if namespace is been pass to command 
     * @param bool
     */
    'console.allow_namespace' => true,
     /**
     * Set your DI new Pimple\Container() for your app to be load before execute
     */
    'console.di' => $container
]);

$console = $container['console'];

$console->run();
```

## Usage
Create a script in your project and setup the Pimple container manually.
```php
#!/usr/bin/env php
<?php

require '[path to composer vendor folder]/autoload.php';

$container = new \Pimple\Container();
$container->register(new \CristianG\PimpleConsole\ServiceProvider(), array(
    'console.name' => 'Console Application',
    'console.version' => "2.0.0",
    'console.classes' => [
        "\App\Console\Version",
        "\App\Console\Sync",
    ]
));

```
Create your class for the namespace provided on console.classes with DI
```php
namespace App\Console;

use Pimple\Container;

use CristianG\PimpleConsole\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Version extends Command
{
    protected ?Container $di = null;

    /**
     * @param  Container  $di
     * @return void
     */
    public function setDi(Container $di): void
    {
        $this->di = $di;
    }

    /**
     * @return Container|null
     */
    public function getDi(): ?Container
    {
        return $this->di;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('app:version');
        $this->setDescription('retrun a version of your app');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Logic to load your app version
        //....
        
        //Return to console the version
        $this->info("App version is : {$APP_VERSION}");
        return Command::SUCCESS;
    }
}
```
Create your class for the namespace provided on console.classes without DI
```php

namespace App\Console;

use Pimple\Container;

use CristianG\PimpleConsole\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Version extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('app:version');
        $this->setDescription('retrun a version of your app');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Logic to load your app version
        //....
        
        //Return to console the version
        $this->info("App version is : {$APP_VERSION}");
        return Command::SUCCESS;
    }
}
```

Extending to Command availabe function
```php
// Output normal text
$this->line($string)

//Output info text
$this->info($string)

//Output info comment
$this->comment($string)

//Output info question
$this->question($string)

//Output info error
$this->error($string)

//Confirm a question
$this->confirm($question, $default = false, $trueAnswerRegex = '/^y/i')

//Asks a question to the user.
$this->ask($question, $default)

// Give the user a single choice from an array of answers.
$this->choice($question, array $choices, $default = null, $attempts = null, $multiple = null)

// Build a table with a style
// Headers : ["Name", "App", "version"]
// Rows An array can be single-dimensional, multidimensional 
$this->table(array $headers, array $rows, $style = 'default')
```