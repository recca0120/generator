<?php

namespace Recca0120\Generator\Console;

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
     * @return string
     */
    protected function buildClass($name)
    {
        $fullBaseClass = $this->option('extend') ?: 'Recca0120\Repository\EloquentRepository';

        $rootNamespace = trim($this->rootNamespace(), '\\');
        $namespace = $this->getNamespace($name);
        $baseClass = ltrim(str_replace($namespace, '', $name), '\\');
        $repositoryContractInterface = $namespace.'\Contracts\\'.$baseClass.'Repository';
        $modelClass = $rootNamespace.'\\'.($this->option('model') ?: $baseClass);

        if (interface_exists($repositoryContractInterface) === false) {
            $this->call('g:repository-contract', ['name' => $baseClass]);
        }

        if (is_null($this->option('without-generator-model')) === true && class_exists($modelClass) === false) {
            $this->call('g:model', ['name' => $baseClass]);
        }

        $render = $this->generator->setFullRepositoryClass($name.'Repository')
            ->setFullBaseClass($fullBaseClass)
            ->setFullModelClass($modelClass)
            ->render($this->getStub());

        $this->renderServiceProvider('Providers/AppServiceProvider');

        return $render;
    }

    /**
     * renderServiceProvider.
     *
     * @param string $className
     * @return string
     */
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
     * @return string
     */
    protected function getPath($name)
    {
        return str_replace('.php', 'Repository.php', parent::getPath($name));
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
            ['without-generator-model', '', InputOption::VALUE_OPTIONAL, 'don\'t generate model.'],
        ];
    }
}
