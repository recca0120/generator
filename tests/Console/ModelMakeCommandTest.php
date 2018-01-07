<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Console\ModelMakeCommand;
use Symfony\Component\Console\Output\BufferedOutput;

class ModelMakeCommandTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testHandle()
    {
        $command = new ModelMakeCommand(
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

        $file = $path.'/'.$name.'.php';
        $directory = $path;
        $fullClass = $rootNamespace.$name;

        $laravel->shouldReceive('basePath')->once()->andReturn($basePath = 'foo');
        $files->shouldReceive('exists')->with($basePath.'/resources/views/generator/app/Model.stub')->once()->andReturn(false);

        $application = m::mock('Symfony\Component\Console\Application');
        $application->shouldReceive('getHelperSet')->andReturn(m::mock('Symfony\Component\Console\Helper\HelperSet'));
        $command->setApplication($application);

        $application->shouldReceive('find')->once()->with('generate:repository')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($name) {
            return str_replace("'", '"', (string) $input) === $name.' --without-generator-model=1 "generate:repository"';
        }), m::any());

        $application->shouldReceive('find')->once()->with('generate:presenter')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($name) {
            return str_replace("'", '"', (string) $input) === $name.' "generate:presenter"';
        }), m::any());

        $files->shouldReceive('exists')->once()->with($file);
        $files->shouldReceive('isDirectory')->once()->with($directory);
        $files->shouldReceive('makeDirectory')->once()->with($directory, 0777, true, true);
        $generator->shouldReceive('setFullModelClass')->once()->with($fullClass)->andReturnSelf();
        $generator->shouldReceive('setFullBaseClass')->once()->with($fullBaseClass)->andReturnSelf();
        $generator->shouldReceive('render')->once()->with(m::on('is_file'))->andReturn($render = 'foo');
        $files->shouldReceive('put')->once()->with($file, $render);

        if (method_exists($command, 'handle') === true) {
            $this->assertNull($command->handle());
        } else {
            $this->assertNull($command->fire());
        }
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
