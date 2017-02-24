<?php

namespace Recca0120\Generator\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Generator;
use Illuminate\Filesystem\Filesystem;

class GeneratorTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testRenderFooBarRepositoryContract()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullRepositoryInterface('App\Repositories\Contracts\FooBarRepository');

        $this->verify(
            $this->render($generator, 'Repositories/Contracts/Repository'),
            'Repositories/Contracts/FooBarRepository'
        );
    }

    public function testRenderFooBarRepository()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullRepositoryClass('App\Repositories\FooBarRepository')
            ->setFullBaseClass('Recca0120\Repository\EloquentRepository')
            ->setFullModelClass('App\FooBar');

        $this->verify(
            $this->render($generator, 'Repositories/Repository'),
            'Repositories/FooBarRepository'
        );
    }

    public function testRenderServiceProvider()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullRepositoryClass('App\Repositories\FooBarRepository')
            ->setFullBaseClass('Recca0120\Repository\EloquentRepository')
            ->setFullModelClass('App\FooBar');

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
        $generator->setFullRepositoryClass('App\Repositories\FooBarWithoutExtendRepository')
            ->setFullModelClass('App\FooBarWithoutExtend');

        $this->verify(
            $this->render($generator, 'Repositories/Repository'),
            'Repositories/FooBarWithoutExtendRepository'
        );
    }

    public function testRenderFooBarModel()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullModelClass('App\FooBar')
            ->setFullBaseClass('Illuminate\Database\Eloquent\Model');

        $this->verify(
            $this->render($generator, 'Model'),
            'FooBar'
        );
    }

    public function testRenderFooBarPresenter()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullPresenterClass('App\Presenters\FooBarPresenter')
            ->setFullBaseClass('Robbo\Presenter\Presenter as RobboPresenter');

        $this->verify(
            $this->render($generator, 'Presenters/Presenter'),
            'Presenters/FooBarPresenter'
        );
    }

    public function testRenderFooBarRequest()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullRequestClass('App\Http\Requests\FooBarRequest')
            ->setFullBaseClass('Illuminate\Foundation\Http\FormRequest');

        $this->verify(
            $this->render($generator, 'Http/Requests/Request'),
            'Http/Requests/FooBarRequest'
        );
    }

    public function testRenderFooBarController()
    {
        $generator = new Generator(new Filesystem());
        $generator
            ->setFullControllerClass('App\Http\Controllers\FooBarController')
            ->setFullBaseClass('App\Http\Controllers\Controller')
            ->setFullRepositoryInterface('App\Repositories\Contracts\FooBarRepository')
            ->setFullRequestClass('App\Http\Requests\FooBarRequest');

        $this->verify(
            $this->render($generator, 'Http/Controllers/Controller'),
            'Http/Controllers/FooBarController'
        );
    }

    public function testRenderNewsController()
    {
        $generator = new Generator(new Filesystem());
        $generator
            ->setFullControllerClass('App\Http\Controllers\NewsController')
            ->setFullBaseClass('App\Http\Controllers\Controller')
            ->setFullRepositoryInterface('App\Repositories\Contracts\NewsRepository')
            ->setFullRequestClass('App\Http\Requests\NewsRequest');

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
        return $generator->render(__DIR__.'/../resources/stubs/'.$className.'.stub');
    }

    protected function getContent($path)
    {
        return strtr(file_get_contents(__DIR__.'/stubs/'.$path.'.php'), [
            "\r\n" => "\n",
        ]);
    }

    protected function verify($content, $path)
    {
        $this->assertSame($this->getContent($path), $content);
    }
}
