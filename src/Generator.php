<?php

namespace Recca0120\Generator;

use Illuminate\Support\Arr;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Generator\Fixers\UseSortFixer;

class Generator
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function generate($command, $name)
    {
        $config = Arr::get($this->config, $command, []);

        return new Code(
            $name,
            $config,
            $this->generateDependencies($name, Arr::get($config, 'dependencies', []))
        );
    }

    private function generateDependencies($name, $dependencies)
    {
        $codes = [];
        foreach ($dependencies as $dependency) {
            $codes[$dependency] = new Code($name, Arr::get($this->config, $dependency, []));
        }

        return $codes;
    }
}
