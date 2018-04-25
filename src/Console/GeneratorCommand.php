<?php

namespace Recca0120\Generator\Console;

use Illuminate\Console\Command;
use Recca0120\Generator\Generator;
use Symfony\Component\Console\Input\InputArgument;

class GeneratorCommand extends Command
{
    private $generator;

    private $command;

    public function __construct(Generator $generator, $command)
    {
        $this->generator = $generator;
        $this->command = $command;
        $this->name = 'generate:'.$command;

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
