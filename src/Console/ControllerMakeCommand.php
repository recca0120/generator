<?php

namespace Recca0120\Generator\Console;

class ControllerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'g:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../resources/stubs/Http/Controllers/Controller.stub';
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
        return $rootNamespace.'\Http\Controllers';
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
        $repositoryContractInterface = $rootNamespace.'\Repositories\Contracts\\'.$baseClass.'Repository';
        $repositoryClass = $rootNamespace.'\Repositories\\'.$baseClass.'Repository';
        $requestClass = $rootNamespace.'\Http\Requests\\'.$baseClass.'Request';

        if (class_exists($repositoryClass) === false) {
            $this->call('g:repository', ['name' => $baseClass]);
        }

        if (class_exists($requestClass) === false) {
            $this->call('g:request', ['name' => $baseClass]);
        }

        return $this->generator->set('DummyFullControllerClass', $name.'Controller')
            ->set('DummyFullRepositoryInterface', $repositoryContractInterface)
            ->set('DummyFullRequestClass', $requestClass)
            ->render($this->getStub());
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
        return str_replace('.php', 'Controller.php', parent::getPath($name));
    }
}
