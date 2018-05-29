<?php

namespace Recca0120\Generator;

use Illuminate\Support\Arr;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Generator\Fixers\UseSortFixer;

class Generator
{
    private $config;

    private $fils;

    private $useSortFixer;

    public function __construct($config, Filesystem $files = null, UseSortFixer $useSortFixer = null)
    {
        $this->config = $config;
        $this->files = $files ?: new Filesystem;
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer();
    }

    public function generate($command, $name)
    {
        $config = Arr::get($this->config, $command, []);

        return new Code(
            $name,
            $config,
            $this->generateDependencies($name, Arr::get($config, 'dependencies', [])),
            $this->files,
            $this->useSortFixer
        );
    }

    private function generateDependencies($name, $dependencies)
    {
        $codes = [];
        foreach ($dependencies as $dependency) {
            $dependencyConfig = Arr::get($this->config, $dependency, []);
            $depencyCodes = $this->generateDependencies($name, array_get($dependencyConfig, 'dependencies', []));

            $codes[$dependency] = new Code(
                $name,
                $dependencyConfig,
                $depencyCodes,
                $this->files,
                $this->useSortFixer
            );

            $codes = array_merge($codes, $depencyCodes);
        }

        return $codes;
    }
}
