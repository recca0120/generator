<?php

namespace Recca0120\Generator\Console;

class RepositoryContractMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:repository-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository contract';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository Contract';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->getStubResource('Repositories/Contracts/Repository.stub');
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
        return $rootNamespace.'\Repositories\Contracts';
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
        return $this->generator->setFullRepositoryInterface($name.'Repository')
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
        return str_replace('.php', 'Repository.php', parent::getPath($name));
    }
}
