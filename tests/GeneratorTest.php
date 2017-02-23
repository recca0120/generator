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

    public function testRenderRepositoryContract()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRepositoryInterface', 'App\Repositories\Contracts\UserProviderRepository');
        $this->verify($generator, 'Repositories/Contracts/Repository');
    }

    public function testRenderRepository()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRepositoryClass', 'App\Repositories\UserProviderRepository')
            ->set('DummyFullModelClass', 'App\UserProvider');

        $this->verify($generator, 'Repositories/Repository');
    }

    public function testRenderModel()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullModelClass', 'App\UserProvider');
        $this->verify($generator, 'Model');
    }

    public function testRenderPresenter()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullPresenterClass', 'App\Presenters\UserProviderPresenter');
        $this->verify($generator, 'Presenters/Presenter');
    }

    public function testRenderRequest()
    {
        $generator = new Generator(new Filesystem());
        $generator->set('DummyFullRequestClass', 'App\Http\Requests\UserProviderRequest');
        $this->verify($generator, 'Http/Requests/Request');
    }

    public function testRenderController()
    {
        $generator = new Generator(new Filesystem());
        $generator
            ->set('DummyFullControllerClass', 'App\Http\Controllers\UserProviderController')
            ->set('DummyFullRepositoryInterface', 'App\Repositories\Contracts\UserProviderRepository')
            ->set('DummyFullRequestClass', 'App\Http\Requests\UserProviderRequest');

        $this->verify($generator, 'Http/Controllers/Controller');
    }

    public function testRenderIndexView()
    {
        $generator = new Generator(new Filesystem());
        $this->verify($generator, 'Views/index.blade');
    }

    protected function verify($generator, $path)
    {
        $this->assertSame(file_get_contents(__DIR__.'/php/'.$path.'.php'), $generator->render(__DIR__.'/../resources/stubs/'.$path.'.stub'));
    }
}
