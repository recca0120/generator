<?php

namespace Recca0120\Generator\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Recca0120\Generator\Console\PresenterMakeCommand;

class PresenterMakeCommandTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testHandle()
    {
        $command = new PresenterMakeCommand(
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

        $defaultNamespace = 'Presenters';
        $directory = $path.'/'.$defaultNamespace;
        $file = $directory.'/'.$name.'Presenter.php';
        $fullClass = $rootNamespace.str_replace('/', '\\', $defaultNamespace).'\\'.$name.'Presenter';

        $laravel->shouldReceive('basePath')->once()->andReturn($basePath = 'foo');
        $files->shouldReceive('exists')->with($basePath.'/resources/views/generator/app/'.$defaultNamespace.'/Presenter.stub')->once()->andReturn(false);

        $files->shouldReceive('exists')->once()->with($file);
        $files->shouldReceive('isDirectory')->once()->with($directory);
        $files->shouldReceive('makeDirectory')->once()->with($directory, 0777, true, true);
        $generator->shouldReceive('setFullPresenterClass')->once()->with($fullClass)->andReturnSelf();
        $generator->shouldReceive('setFullBaseClass')->once()->with($fullBaseClass)->andReturnSelf();
        $generator->shouldReceive('render')->once()->with(m::on('is_file'))->andReturn($render = 'foo');
        $files->shouldReceive('put')->once()->with($file, $render);

        $this->assertNull($command->handle());
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
