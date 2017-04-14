<?php

namespace Recca0120\Generator\Console;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ViewMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new view';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'View';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->getStubResource('views/scaffold/'.$this->view().'.blade.stub', 'resources');
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
        $requestClass = $rootNamespace.'\Http\Requests\\'.$baseClass.'Request';

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
        $name = Str::plural(str_replace($this->rootNamespace().'Http\Controllers\\', '', $name));
        $path = $this->laravel->basePath().'/resources/views/';

        return $path .= implode('/', array_map(function ($path) {
            return Str::snake($path, '-');
        }, explode('\\', $name))).'/'.$this->view().'.blade.php';
    }

    /**
     * view.
     *
     * @return string
     */
    protected function view()
    {
        return (string) $this->option('view') ?: 'index';
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
