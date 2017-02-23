<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Console\ModelMakeCommand;
use Symfony\Component\Console\Output\BufferedOutput;

class ModelMakeCommandTest extends TestCase
{
    protected function mockProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testFire()
    {
        $command = new ModelMakeCommand(
            $filesystem = m::mock('Illuminate\Filesystem\Filesystem'),
            $generator = m::mock('Recca0120\Generator\Generator')
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput());

        $command->setLaravel($laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess'));

        $input->shouldReceive('getArgument')->with('name')->andReturn($name = 'foo');
        $laravel->shouldReceive('getNamespace')->andReturn($rootNamespace = 'fooNamespace\\');
        $laravel->shouldReceive('offsetGet')->twice()->with('path')->andReturn($path = 'foo');

        $file = $path.'/'.$name.'.php';
        $directory = $path;
        $fullClass = $rootNamespace.$name;

        $application = m::mock('Symfony\Component\Console\Application');
        $application->shouldReceive('getHelperSet')->andReturn(m::mock('Symfony\Component\Console\Helper\HelperSet'));
        $command->setApplication($application);

        $application->shouldReceive('find')->once()->with('g:presenter')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($fullClass) {
            return (string) $input === basename($fullClass).' "g:presenter"';
        }), m::any());

        $filesystem->shouldReceive('exists')->once()->with($file);
        $filesystem->shouldReceive('isDirectory')->once()->with($directory);
        $filesystem->shouldReceive('makeDirectory')->once()->with($directory, 0777, true, true);
        $generator->shouldReceive('set')->once()->with('DummyFullModelClass', $fullClass)->andReturnSelf();
        $generator->shouldReceive('render')->once()->with(m::on('is_file'))->andReturn($render = 'foo');
        $filesystem->shouldReceive('put')->once()->with($file, $render);

        $command->fire();
    }
}
