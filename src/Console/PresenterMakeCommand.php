<?php

namespace Recca0120\Generator\Console;

use Symfony\Component\Console\Input\InputOption;

class PresenterMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:presenter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new presenter';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Presenter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->getStubResource('Presenters/Presenter.stub');
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
        return $rootNamespace.'\Presenters';
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
        $fullBaseClass = $this->option('extend') ?: 'Robbo\Presenter\Presenter as RobboPresenter';

        return $this->generator->setFullPresenterClass($name.'Presenter')
            ->setFullBaseClass($fullBaseClass)
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
        return str_replace('.php', 'Presenter.php', parent::getPath($name));
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
