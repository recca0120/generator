<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Recca0120\Generator\Console\ControllerMakeCommand;

class ControllerMakeCommandTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $command = new ControllerMakeCommand(
            $filesystem = m::mock('Illuminate\Filesystem\Filesystem'),
            $generator = m::mock('Recca0120\Generator\Generator')
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput());

        $command->setLaravel($laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess'));

        $input->shouldReceive('getArgument')->with('name')->andReturn($name = 'foo');
        $laravel->shouldReceive('getNamespace')->andReturn($rootNamespace = 'fooNamespace\\');
        $laravel->shouldReceive('offsetGet')->with('path')->andReturn($path = 'foo');

        $input->shouldReceive('getOption')->with('extend')->andReturn($fullBaseClass = 'foo');

        $defaultNamespace = 'Http/Controllers';
        $directory = $path.'/'.$defaultNamespace;
        $file = $directory.'/'.$name.'Controller.php';
        $fullClass = $rootNamespace.str_replace('/', '\\', $defaultNamespace).'\\'.$name.'Controller';

        $laravel->shouldReceive('basePath')->once()->andReturn($basePath = 'foo');
        $filesystem->shouldReceive('exists')->with($basePath.'/resources/views/generator/'.$defaultNamespace.'/Controller.stub')->once()->andReturn(false);

        $application = m::mock('Symfony\Component\Console\Application');
        $application->shouldReceive('getHelperSet')->andReturn(m::mock('Symfony\Component\Console\Helper\HelperSet'));
        $command->setApplication($application);

        $application->shouldReceive('find')->once()->with('generate:repository')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($name) {
            return str_replace('"', "'", (string) $input) === $name.' \'generate:repository\'';
        }), m::any());

        $application->shouldReceive('find')->once()->with('generate:request')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($name) {
            return str_replace('"', "'", (string) $input) === $name.' \'generate:request\'';
        }), m::any());

        $filesystem->shouldReceive('exists')->once()->with($file);
        $filesystem->shouldReceive('isDirectory')->once()->with($directory);
        $filesystem->shouldReceive('makeDirectory')->once()->with($directory, 0777, true, true);
        $generator->shouldReceive('setFullControllerClass')->once()->with($fullClass)->andReturnSelf();
        $generator->shouldReceive('setFullBaseClass')->once()->with($fullBaseClass)->andReturnSelf();
        $generator->shouldReceive('setFullRepositoryInterface')->once()->with($rootNamespace.'Repositories\Contracts\\'.$name.'Repository')->andReturnSelf();
        $generator->shouldReceive('setFullRequestClass')->once()->with($rootNamespace.'Http\Requests\\'.$name.'Request')->andReturnSelf();
        $generator->shouldReceive('render')->once()->with(m::on('is_file'))->andReturn($render = 'foo');
        $filesystem->shouldReceive('put')->once()->with($file, $render);

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
