<?php

namespace Recca0120\Generator\Console;

use Illuminate\Support\Str;
use Recca0120\Generator\Generator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand as BaseCommand;

abstract class GeneratorCommand extends BaseCommand
{
    /**
     * $generator.
     *
     * @var \Recca0120\Generator\Generator
     */
    protected $generator;

    /**
     * Create a new controller creator command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Recca0120\Generator\Generator
     */
    public function __construct(Filesystem $files, Generator $generator)
    {
        parent::__construct($files);

        $this->generator = $generator;
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->laravel->getNamespace();
    }

    /**
     * getStubResource.
     *
     * @param string $stub
     * @param string $folder
     * @return string
     */
    protected function getStubResource($stub, $folder = 'app')
    {
        $root = $this->laravel->basePath().'/resources/views/generator/'.$folder.'/';

        return $this->files->exists($root.$stub) === true
            ? $root.$stub
            : __DIR__.'/../../resources/stubs/'.$folder.'/'.$stub;
    }
}
