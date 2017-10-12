<?php

namespace Recca0120\Generator\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ScaffoldMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new scaffold';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->call('generate:controller', ['name' => $name]);
        $this->call('generate:view', ['name' => $name, '--view' => 'index']);
        $this->call('generate:view', ['name' => $name, '--view' => 'create']);
        $this->call('generate:view', ['name' => $name, '--view' => 'edit']);
        $this->call('generate:view', ['name' => $name, '--view' => '_form']);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['extend', '', InputOption::VALUE_OPTIONAL, 'controller extend.'],
            ['view', '', InputOption::VALUE_OPTIONAL, 'view'],
        ];
    }
}
