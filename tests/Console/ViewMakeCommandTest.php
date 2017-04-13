<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Console\ViewMakeCommand;
use Symfony\Component\Console\Output\BufferedOutput;

class ViewMakeCommandTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $command = new ViewMakeCommand(
            $filesystem = m::mock('Illuminate\Filesystem\Filesystem'),
            $generator = m::mock('Recca0120\Generator\Generator')
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput());

        $command->setLaravel($laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess'));
        $laravel->shouldReceive('basePath')->twice()->andReturn($basePath = 'foo');

        $input->shouldReceive('getArgument')->with('name')->andReturn($name = 'foo');
        $laravel->shouldReceive('getNamespace')->andReturn($rootNamespace = 'fooNamespace\\');
        $laravel->shouldReceive('offsetGet')->with('path')->andReturn($path = 'foo');

        $input->shouldReceive('getOption')->with('extend')->andReturn($fullBaseClass = 'foo');
        $input->shouldReceive('getOption')->with('view')->andReturn($view = 'index');

        $defaultNamespace = 'Http/Controllers';
        $directory = $basePath.'/resources/views/'.$path;
        $file = $directory.'/'.$view.'.blade.php';
        $fullClass = $rootNamespace.str_replace('/', '\\', $defaultNamespace).'\\'.$name.'Controller';

        $laravel->shouldReceive('basePath')->andReturn($basePath = 'foo');
        $filesystem->shouldReceive('exists')->with($basePath.'/resources/views/generator/resources/views/scaffold/'.$view.'.blade.stub')->once()->andReturn(false);

        $application = m::mock('Symfony\Component\Console\Application');
        $application->shouldReceive('getHelperSet')->andReturn(m::mock('Symfony\Component\Console\Helper\HelperSet'));
        $command->setApplication($application);

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
