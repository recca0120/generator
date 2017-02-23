<?php

namespace Recca0120\Generator\Tests;

use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Generator;

class GeneratorTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testRenderFooBarRepositoryContract()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRepositoryInterface', 'App\Repositories\Contracts\FooBarRepository');

        $this->verify(
            $this->render($generator, 'Repositories/Contracts/Repository'),
            'Repositories/Contracts/FooBarRepository'
        );
    }

    public function testRenderFooBarRepository()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRepositoryClass', 'App\Repositories\FooBarRepository')
            ->set('DummyFullBaseClass', 'Recca0120\Repository\EloquentRepository')
            ->set('DummyFullModelClass', 'App\FooBar');

        $this->verify(
            $this->render($generator, 'Repositories/Repository'),
            'Repositories/FooBarRepository'
        );
    }

    public function testRenderServiceProvider()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRepositoryClass', 'App\Repositories\FooBarRepository')
            ->set('DummyFullBaseClass', 'Recca0120\Repository\EloquentRepository')
            ->set('DummyFullModelClass', 'App\FooBar');

        $this->verify(
            $generator->renderServiceProvider(
                $this->getContent('Providers/MockServiceProvider')
            ),
            'Providers/AppServiceProvider'
        );
    }

    public function testRenderFooBarWithoutExtendRepository()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRepositoryClass', 'App\Repositories\FooBarWithoutExtendRepository')
            ->set('DummyFullModelClass', 'App\FooBarWithoutExtend');

        $this->verify(
            $this->render($generator, 'Repositories/Repository'),
            'Repositories/FooBarWithoutExtendRepository'
        );
    }

    public function testRenderFooBarModel()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullModelClass', 'App\FooBar');

        $this->verify(
            $this->render($generator, 'Model'),
            'FooBar'
        );
    }

    public function testRenderFooBarPresenter()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullPresenterClass', 'App\Presenters\FooBarPresenter');

        $this->verify(
            $this->render($generator, 'Presenters/Presenter'),
            'Presenters/FooBarPresenter'
        );
    }

    public function testRenderFooBarRequest()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRequestClass', 'App\Http\Requests\FooBarRequest');

        $this->verify(
            $this->render($generator, 'Http/Requests/Request'),
            'Http/Requests/FooBarRequest'
        );
    }

    public function testRenderFooBarController()
    {
        $generator = new Generator(new Filesystem());
        $generator
            ->set('DummyFullControllerClass', 'App\Http\Controllers\FooBarController')
            ->set('DummyFullRepositoryInterface', 'App\Repositories\Contracts\FooBarRepository')
            ->set('DummyFullRequestClass', 'App\Http\Requests\FooBarRequest')
            ->set('DummyFullBaseClass', 'App\Http\Controllers\Controller');

        $this->verify(
            $this->render($generator, 'Http/Controllers/Controller'),
            'Http/Controllers/FooBarController'
        );
    }

    public function testRenderNewsController()
    {
        $generator = new Generator(new Filesystem());
        $generator
            ->set('DummyFullControllerClass', 'App\Http\Controllers\NewsController')
            ->set('DummyFullRepositoryInterface', 'App\Repositories\Contracts\NewsRepository')
            ->set('DummyFullRequestClass', 'App\Http\Requests\NewsRequest');

        $this->verify(
            $this->render($generator, 'Http/Controllers/Controller'),
            'Http/Controllers/NewsController'
        );
    }

    // public function testRenderViewIndex()
    // {
    //     $generator = new Generator(new Filesystem());
    //     $this->verify(
    //         $this->render($generator, 'Views/index.blade'),
    //         'Views/index.blade'
    //     );
    // }

    protected function render($generator, $className)
    {
        return $generator->render(__DIR__.'/../src/Console/stubs/'.$className.'.stub');
    }

    protected function getContent($path)
    {
        return file_get_contents(__DIR__.'/stubs/'.$path.'.php');
    }

    protected function verify($content, $path)
    {
        $this->assertSame($this->getContent($path), $content);
    }
}
