<?php

namespace Recca0120\Generator\Console;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'g:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../resources/stubs/Repositories/Repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories';
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
        $modelClass = $this->parseModel(
            $this->option('model') ?: $this->rootNamespace().class_basename($name)
        );

        $fullBaseClass = $this->option('extend') ?: 'Recca0120\Repository\EloquentRepository';

        $rootNamespace = trim($this->rootNamespace(), '\\');
        $namespace = $this->getNamespace($name);
        $baseClass = ltrim(str_replace($namespace, '', $name), '\\');
        $repositoryContractInterface = $namespace.'\Contracts\\'.$baseClass.'Repository';
        $modelClass = $rootNamespace.'\\'.$baseClass;

        if (interface_exists($repositoryContractInterface) === false) {
            $this->call('g:repository-contract', ['name' => $baseClass]);
        }

        if (is_null($this->option('without-generator-model')) === true && class_exists($modelClass) === false) {
            $this->call('g:model', ['name' => $baseClass]);
        }

        $render = $this->generator->set('DummyFullRepositoryClass', $name.'Repository')
            ->set('DummyFullBaseClass', $fullBaseClass)
            ->set('DummyFullModelClass', $modelClass)
            ->render($this->getStub());

        $this->renderServiceProvider('Providers/AppServiceProvider');

        return $render;
    }

    protected function renderServiceProvider($className)
    {
        $file = parent::getPath($className);

        $this->files->put(
            $file,
            $this->generator->renderServiceProvider(
                $this->files->get($file)
            )
        );
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        return str_replace('.php', 'Repository.php', parent::getPath($name));
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     *
     * @return string
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a repository for the given model.'],

            ['extend', '', InputOption::VALUE_OPTIONAL, 'repository extend.'],

            ['without-generator-model', '', InputOption::VALUE_OPTIONAL, 'don\'t generate model.']
        ];
    }
}
