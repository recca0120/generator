<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Recca0120\Generator\Console\ControllerMakeCommand;

class ControllerMakeCommandTest extends TestCase
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
        $command = new ControllerMakeCommand(
            $filesystem = m::mock('Illuminate\Filesystem\Filesystem'),
            $generator = m::mock('Recca0120\Generator\Generator')
        );

        $this->mockProperty($command, 'input', $input = m::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->mockProperty($command, 'output', $output = new BufferedOutput());

        $command->setLaravel($laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess'));

        $input->shouldReceive('getArgument')->with('name')->andReturn($name = 'foo');
        $laravel->shouldReceive('getNamespace')->andReturn($rootNamespace = 'fooNamespace\\');
        $laravel->shouldReceive('offsetGet')->twice()->with('path')->andReturn($path = 'foo');

        $defaultNamespace = 'Http/Controllers';
        $directory = $path.'/'.$defaultNamespace;
        $file = $directory.'/'.$name.'Controller.php';
        $fullClass = $rootNamespace.str_replace('/', '\\', $defaultNamespace).'\\'.$name.'Controller';

        $application = m::mock('Symfony\Component\Console\Application');
        $application->shouldReceive('getHelperSet')->andReturn(m::mock('Symfony\Component\Console\Helper\HelperSet'));
        $command->setApplication($application);

        $application->shouldReceive('find')->once()->with('g:repository')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($fullClass) {
            return (string) $input === basename(str_replace('Controller', '', $fullClass)).' "g:repository"';
        }), m::any());

        $application->shouldReceive('find')->once()->with('g:request')->andReturnSelf();
        $application->shouldReceive('run')->once()->with(m::on(function ($input) use ($fullClass) {
            return (string) $input === basename(str_replace('Controller', '', $fullClass)).' "g:request"';
        }), m::any());

        $filesystem->shouldReceive('exists')->once()->with($file);
        $filesystem->shouldReceive('isDirectory')->once()->with($directory);
        $filesystem->shouldReceive('makeDirectory')->once()->with($directory, 0777, true, true);
        $generator->shouldReceive('set')->once()->with('DummyFullControllerClass', $fullClass)->andReturnSelf();
        $generator->shouldReceive('set')->once()->with('DummyFullRepositoryInterface', $rootNamespace.'Repositories\Contracts\\'.$name.'Repository')->andReturnSelf();
        $generator->shouldReceive('set')->once()->with('DummyFullRequestClass', $rootNamespace.'Http\Requests\\'.$name.'Request')->andReturnSelf();
        $generator->shouldReceive('render')->once()->with(m::type('string'))->andReturn($render = 'foo');
        $filesystem->shouldReceive('put')->once()->with($file, $render);

        $command->fire();
    }
}
