<?php

namespace Recca0120\Generator\Console;

use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:controller';

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
        return $this->getStubResource('Http/Controllers/Controller.stub');
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
        $fullBaseClass = $this->getNamespace($name).'\Controller';
        $fullBaseClass = class_exists($fullBaseClass) === true ?
            $fullBaseClass : 'App\Http\Controllers\Controller';

        $fullBaseClass = (string) $this->option('extend') ?: $fullBaseClass;
        $rootNamespace = trim($this->rootNamespace(), '\\');
        $namespace = $this->getNamespace($name);
        $baseClass = ltrim(str_replace($namespace, '', $name), '\\');
        $repositoryContractInterface = $rootNamespace.'\Repositories\Contracts\\'.$baseClass.'Repository';
        $repositoryClass = $rootNamespace.'\Repositories\\'.$baseClass.'Repository';
        $requestClass = $rootNamespace.'\Http\Requests\\'.$baseClass.'Request';

        if (class_exists($repositoryClass) === false) {
            $this->call('generate:repository', ['name' => $baseClass]);
        }

        if (class_exists($requestClass) === false) {
            $this->call('generate:request', ['name' => $baseClass]);
        }

        return $this->generator->setFullControllerClass($name.'Controller')
            ->setFullBaseClass($fullBaseClass)
            ->setFullRepositoryInterface($repositoryContractInterface)
            ->setFullRequestClass($requestClass)
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

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['extend', '', InputOption::VALUE_OPTIONAL, 'controller extend.'],
        ];
    }
}
