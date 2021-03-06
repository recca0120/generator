<?php

namespace Recca0120\Generator\Console;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Recca0120\Generator\Generator;
use Symfony\Component\Console\Input\InputArgument;

class GeneratorCommand extends Command
{
    private $generator;

    private $command;

    public function __construct(Generator $generator, $command, $config = [])
    {
        $commandPrefix = Arr::get($config, 'command_prefix', 'generate');
        $commandPrefix = empty($commandPrefix) === false ? $commandPrefix.':' : '';

        $this->generator = $generator;
        $this->command = $command;
        $this->name = $commandPrefix.$command;

        parent::__construct();
    }

    public function handle()
    {
        $this->generator->generate($this->command, $this->argument('name'))->store();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'name'],
        ];
    }
}
