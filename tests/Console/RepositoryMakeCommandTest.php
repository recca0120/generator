<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Recca0120\Generator\Console\RepositoryMakeCommand;

class RepositoryMakeCommandTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $command = new RepositoryMakeCommand(
            $filesystem = m::mock('Illuminate\Filesystem\Filesystem'),
            $generator = m::mock('Recca0120\Generator\Generator')
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput());

        $command->setLaravel($laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess'));

        $input->shouldReceive('getArgument')->with('name')->andReturn($name = 'foo');
        $laravel->shouldReceive('getNamespace')->andReturn($rootNamespace = 'fooNamespace\\');
        $laravel->shouldReceive('offsetGet')->with('path')->andReturn($path = 'foo');

        $input->shouldReceive('getOption')->with('model')->andReturn($model = 'foo');
        $input->shouldReceive('getOption')->with('extend')->andReturn($fullBaseClass = 'foo');
        $input->shouldReceive('getOption')->with('without-generator-model')->andReturn(null);

        $defaultNamespace = 'Repositories';
        $directory = $path.'/'.$defaultNamespace;
        $file = $directory.'/'.$name.'Repository.php';
        $fullClass = $rootNamespace.str_replace('/', '\\', $defaultNamespace).'\\'.$name.'Repository';

        $application = m::mock('Symfony\Component\Console\Application');
        $application->shouldReceive('getHelperSet')->andReturn(m::mock('Symfony\Component\Console\Helper\HelperSet'));
        $command->setApplication($application);

        $application->shouldReceive('find')->once()->with('generate:repository-contract')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($name) {
            return str_replace("'", '"', (string) $input) === $name.' "generate:repository-contract"';
        }), m::any());

        $application->shouldReceive('find')->once()->with('generate:model')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($name) {
            return str_replace("'", '"', (string) $input) === $name.' "generate:model"';
        }), m::any());

        $filesystem->shouldReceive('exists')->once()->with($file);
        $filesystem->shouldReceive('isDirectory')->once()->with($directory);
        $filesystem->shouldReceive('makeDirectory')->once()->with($directory, 0777, true, true);
        $generator->shouldReceive('setFullRepositoryClass')->once()->with($fullClass)->andReturnSelf();
        $generator->shouldReceive('setFullBaseClass')->once()->with($fullBaseClass)->andReturnSelf();
        $generator->shouldReceive('setFullModelClass')->once()->with($rootNamespace.$model)->andReturnSelf();
        $generator->shouldReceive('render')->once()->with(m::on('is_file'))->andReturn($render = 'foo');
        $filesystem->shouldReceive('put')->once()->with($file, $render);

        $filesystem->shouldReceive('get')->once()->with($path.'/Providers/AppServiceProvider.php')->andReturn($content = 'foo');
        $generator->shouldReceive('renderServiceProvider')->once()->with($content)->andReturn($registerContent = 'foo');
        $filesystem->shouldReceive('put')->once()->with($path.'/Providers/AppServiceProvider.php', $registerContent);

        $command->fire();
    }

    protected function mockProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }
}
