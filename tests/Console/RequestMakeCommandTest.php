<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Console\RequestMakeCommand;
use Symfony\Component\Console\Output\BufferedOutput;

class RequestMakeCommandTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $command = new RequestMakeCommand(
            $files = m::mock('Illuminate\Filesystem\Filesystem'),
            $generator = m::mock('Recca0120\Generator\Generator')
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput());

        $command->setLaravel($laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess'));

        $input->shouldReceive('getArgument')->with('name')->andReturn($name = 'foo');
        $laravel->shouldReceive('getNamespace')->andReturn($rootNamespace = 'fooNamespace\\');
        $laravel->shouldReceive('offsetGet')->with('path')->andReturn($path = 'foo');

        $input->shouldReceive('getOption')->with('extend')->andReturn($fullBaseClass = 'foo');

        $defaultNamespace = 'Http/Requests';
        $directory = $path.'/'.$defaultNamespace;
        $file = $directory.'/'.$name.'Request.php';
        $fullClass = $rootNamespace.str_replace('/', '\\', $defaultNamespace).'\\'.$name.'Request';

        $laravel->shouldReceive('basePath')->once()->andReturn($basePath = 'foo');
        $files->shouldReceive('exists')->with($basePath.'/resources/views/generator/app/'.$defaultNamespace.'/Request.stub')->once()->andReturn(false);

        $files->shouldReceive('exists')->once()->with($file);
        $files->shouldReceive('isDirectory')->once()->with($directory);
        $files->shouldReceive('makeDirectory')->once()->with($directory, 0777, true, true);
        $generator->shouldReceive('setFullRequestClass')->once()->with($fullClass)->andReturnSelf();
        $generator->shouldReceive('setFullBaseClass')->once()->with($fullBaseClass)->andReturnSelf();
        $generator->shouldReceive('render')->once()->with(m::on('is_file'))->andReturn($render = 'foo');
        $files->shouldReceive('put')->once()->with($file, $render);

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
