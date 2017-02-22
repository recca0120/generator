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

    protected function setUp()
    {
        $this->generator = new Generator(new Filesystem);
    }

    public function testRenderRepositoryContract()
    {
        $this->generator->set('DummyFullRepositoryInterface', 'App\Repositories\Contracts\UserProviderRepository');
        $this->verify('Repositories/Contracts/Repository');
    }

    public function testRenderRepository()
    {
        $this->generator->set('DummyFullRepositoryClass', 'App\Repositories\UserProviderRepository')
            ->set('DummyFullModelClass', 'App\UserProvider');

        $this->verify('Repositories/Repository');
    }

    public function testRenderModel()
    {
        $this->generator->set('DummyFullModelClass', 'App\UserProvider');
        $this->verify('Model');
    }

    public function testRenderPresenter()
    {
        $this->generator->set('DummyFullPresenterClass', 'App\Presenters\UserProviderPresenter');
        $this->verify('Presenters/Presenter');
    }

    public function testRenderRequest()
    {
        $this->generator->set('DummyFullRequestClass', 'App\Http\Requests\UserProviderRequest');
        $this->verify('Http/Requests/Request');
    }

    public function testRenderController()
    {
        $this->generator
            ->set('DummyFullControllerClass', 'App\Http\Controllers\UserProviderController')
            ->set('DummyFullRepositoryInterface', 'App\Repositories\Contracts\UserProviderRepository')
            ->set('DummyFullRequestClass', 'App\Http\Requests\UserProviderRequest')
            ;

        $this->verify('Http/Controllers/Controller');
    }

    public function testRenderIndexView()
    {
        $this->verify('Views/index.blade');
    }

    protected function verify($path)
    {
        $this->assertSame(file_get_contents(__DIR__.'/php/'.$path.'.php'), $this->generator->render($path));
    }
}
