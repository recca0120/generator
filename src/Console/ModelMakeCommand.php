<?php

namespace Recca0120\Generator\Console;

class ModelMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'g:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../resources/stubs/Model.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $rootNamespace = trim($this->rootNamespace(), '\\');
        $namespace = $this->getNamespace($name);
        $baseClass = ltrim(str_replace($namespace, '', $name), '\\');
        $repositoryClass = $rootNamespace.'\Repositories\\'.$baseClass.'Repository';
        $presenterClass = $rootNamespace.'\Presenters\\'.$baseClass.'Presenter';

        if (class_exists($repositoryClass) === false) {
            $this->call('g:repository', [
                'name' => $baseClass,
                '--without-generator-model' => true,
            ]);
        }

        if (class_exists($presenterClass) === false) {
            $this->call('g:presenter', ['name' => $baseClass]);
        }

        return $this->generator->setFullModelClass($name)
            ->render($this->getStub());
    }
}
