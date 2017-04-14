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
            [$generator, 'Repositories/Contracts/Repository'],
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
            [$generator, 'Repositories/Repository'],
            'Repositories/FooBarRepository'
        );
    }

    public function testRenderServiceProvider()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullRepositoryClass('App\Repositories\FooBarRepository')
            ->setFullBaseClass('Recca0120\Repository\EloquentRepository')
            ->setFullModelClass('App\FooBar');

        $this->assertSame(
            $this->getContent('Providers/AppServiceProvider'),
            $generator->renderServiceProvider(
                $this->getContent('Providers/MockServiceProvider')
            )
        );
    }

    public function testRenderFooBarWithoutExtendRepository()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullRepositoryClass('App\Repositories\FooBarWithoutExtendRepository')
            ->setFullModelClass('App\FooBarWithoutExtend');

        $this->verify(
            [$generator, 'Repositories/Repository'],
            'Repositories/FooBarWithoutExtendRepository'
        );
    }

    public function testRenderFooBarModel()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullModelClass('App\FooBar')
            ->setFullBaseClass('Illuminate\Database\Eloquent\Model');

        $this->verify(
            [$generator, 'Model'],
            'FooBar'
        );
    }

    public function testRenderFooBarPresenter()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullPresenterClass('App\Presenters\FooBarPresenter')
            ->setFullBaseClass('Robbo\Presenter\Presenter as RobboPresenter');

        $this->verify(
            [$generator, 'Presenters/Presenter'],
            'Presenters/FooBarPresenter'
        );
    }

    public function testRenderFooBarRequest()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullRequestClass('App\Http\Requests\FooBarRequest')
            ->setFullBaseClass('Illuminate\Foundation\Http\FormRequest');

        $this->verify(
            [$generator, 'Http/Requests/Request'],
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
            [$generator, 'Http/Controllers/Controller'],
            'Http/Controllers/FooBarController'
        );
    }

    public function testRenderAdminFooBarController()
    {
        $generator = new Generator(new Filesystem());
        $generator
            ->setFullControllerClass('App\Http\Controllers\Admin\FooBarController')
            ->setFullBaseClass('App\Http\Controllers\Admin\Controller')
            ->setFullRepositoryInterface('App\Repositories\Contracts\FooBarRepository')
            ->setFullRequestClass('App\Http\Requests\FooBarRequest');

        $this->verify(
            [$generator, 'Http/Controllers/Controller'],
            'Http/Controllers/Admin/FooBarController'
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
            [$generator, 'Http/Controllers/Controller'],
            'Http/Controllers/NewsController'
        );
    }

    public function testRenderViewIndex()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullControllerClass('App\Http\Controllers\Admin\FooBarController');

        $this->verify(
            [$generator, 'views/scaffold/index.blade'],
            'views/foo-bars/index.blade',
            'resources'
        );
    }

    public function testRenderViewCreate()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullControllerClass('App\Http\Controllers\Admin\FooBarController');

        $this->verify(
            [$generator, 'views/scaffold/create.blade'],
            'views/foo-bars/create.blade',
            'resources'
        );
    }

    public function testRenderViewEdit()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullControllerClass('App\Http\Controllers\Admin\FooBarController');

        $this->verify(
            [$generator, 'views/scaffold/edit.blade'],
            'views/foo-bars/edit.blade',
            'resources'
        );
    }

    public function testRenderViewForm()
    {
        $generator = new Generator(new Filesystem());
        $generator->setFullControllerClass('App\Http\Controllers\Admin\FooBarController');

        $this->verify(
            [$generator, 'views/scaffold/_form.blade'],
            'views/foo-bars/_form.blade',
            'resources'
        );
    }

    protected function render($generator, $className, $folder = 'app')
    {
        return $generator->render(__DIR__.'/../resources/stubs/'.$folder.'/'.$className.'.stub');
    }

    protected function getContent($path, $folder = 'app')
    {
        return strtr(file_get_contents(__DIR__.'/fixtures/'.$folder.'/'.$path.'.php'), [
            "\r\n" => "\n",
        ]);
    }

    protected function verify($command, $path, $folder = 'app')
    {
        $this->assertSame(
            $this->getContent($path, $folder),
            $this->render($command[0], $command[1], $folder)
        );
    }
}
