<?php

namespace Recca0120\Generator;

use Recca0120\Generator\Console\GeneratorCommand;

class CommandFactory
{
    private $app;

    public function __construct($config, Generator $generator, $app)
    {
        $this->config = $config;
        $this->generator = $generator;
        $this->app = $app;
    }

    public function create()
    {
        return array_map(function ($name) {
            $command = new GeneratorCommand($this->generator, $name, $this->config[$name]);

            $instanceName = 'recca0120.generator.'.$name;
            $this->app->instance($instanceName, $command);

            return $instanceName;
        }, array_keys($this->config));
    }
}
