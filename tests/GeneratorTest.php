<?php

namespace Recca0120\Generator\Tests;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Generator;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class GeneratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup();

        mkdir($this->root->url().'/app');
        mkdir($this->root->url().'/app/Providers');
        file_put_contents(
            $this->root->url().'/app/Providers/AppServiceProvider.php',
            file_get_contents(__DIR__.'/fixtures/app/Providers/AppServiceProvider.php')
        );

        parent::setUp();
        $this->config = [
            'model' => [
                'path' => $this->app_path(''),
                'stub' => resource_path('stubs/app/Model.stub'),
                'attributes' => [
                    'namespace' => 'App',
                    'extends_qualified_class' => \Illuminate\Database\Eloquent\Model::class,
                ],
            ],
            'repository-contract' => [
                'path' => $this->app_path('Repositories/Contracts'),
                'stub' => resource_path('stubs/app/Repositories/Contracts/Repository.stub'),
                'suffix' => 'Repository',
                'sort' => false,
                'attributes' => [
                    'namespace' => 'App\Repositories\Contracts',
                ],
            ],
            'repository' => [
                'path' => $this->app_path('Repositories'),
                'stub' => resource_path('stubs/app/Repositories/Repository.stub'),
                'suffix' => 'Repository',
                'attributes' => [
                    'namespace' => 'App\Repositories',
                    'extends_qualified_class' => \Recca0120\Repository\EloquentRepository::class,
                ],
                'dependencies' => [
                    'model',
                    'repository-contract',
                ],
                'plugins' => [
                    \Recca0120\Generator\Plugins\ServiceProviderRegister::class => [
                        'path' => $this->app_path('Providers/AppServiceProvider.php'),
                    ],
                ],
            ],
            'controller' => [
                'path' => app_path('Http/Controllers'),
                'stub' => resource_path('stubs/app/Http/Controllers/Controller.stub'),
                'suffix' => 'Controller',
                'attributes' => [
                    'namespace' => 'App\Http\Controllers',
                    'extends_qualified_class' => \Illuminate\Http\Controller::class,
                ],
                'dependencies' => [
                    'repository',
                ],
            ]
        ];
    }

    /** @test */
    public function it_should_generate_model()
    {
        $generator = new Generator($this->config);
        $name = 'FooBar';
        $command = 'model';
        $code = $generator->generate($command, $name);

        $this->assertSame(
            $this->lineEncoding($code->render()),
            $this->getFixture('app/FooBar.php')
        );
    }

    /** @test */
    public function it_should_generate_repository_contract()
    {
        $generator = new Generator($this->config);
        $name = 'FooBar';
        $command = 'repository-contract';
        $code = $generator->generate($command, $name);

        $this->assertSame(
            $this->lineEncoding($code->render()),
            $this->getFixture('app/Repositories/Contracts/FooBarRepository.php')
        );
    }

    /** @test */
    public function it_should_generate_repository()
    {
        $generator = new Generator($this->config);
        $name = 'FooBar';
        $command = 'repository';
        $code = $generator->generate($command, $name);

        $this->assertSame(
            $this->lineEncoding($code->render()),
            $this->getFixture('app/Repositories/FooBarRepository.php')
        );

        $this->assertSame(
            $this->lineEncoding(file_get_contents($this->app_path('Providers/AppServiceProvider.php'))),
            $this->getFixture('app/Providers/AppServiceProviderSnapshot.php')
        );
    }

    /** @test */
    public function it_should_store_code_and_depencencies()
    {
        $generator = new Generator($this->config);
        $name = 'FooBar';
        $command = 'repository';
        $code = $generator->generate($command, $name);

        $code->store();

        $this->assertTrue($this->root->hasChild('app/FooBar.php'));
        $this->assertTrue($this->root->hasChild('app/Repositories/FooBarRepository.php'));
        $this->assertTrue($this->root->hasChild('app/Repositories/Contracts/FooBarRepository.php'));

        $this->assertSame(
            $this->lineEncoding($this->root->getChild('app/FooBar.php')->getContent()),
            $this->getFixture('app/FooBar.php')
        );

        $this->assertSame(
            $this->lineEncoding($this->root->getChild('app/Repositories/FooBarRepository.php')->getContent()),
            $this->getFixture('app/Repositories/FooBarRepository.php')
        );

        $this->assertSame(
            $this->lineEncoding($this->root->getChild('app/Repositories/Contracts/FooBarRepository.php')->getContent()),
            $this->getFixture('app/Repositories/Contracts/FooBarRepository.php')
        );
    }

    /** @test */
    public function it_should_generate_controller()
    {
        $generator = new Generator($this->config);
        $name = 'FooBar';
        $command = 'controller';
        $code = $generator->generate($command, $name);

        $this->assertSame(
            $this->lineEncoding($code->render()),
            $this->getFixture('app/Http/Controllers/FooBarController.php')
        );
    }

    private function getFixture($path)
    {
        return $this->lineEncoding(file_get_contents(__DIR__.'/fixtures/'.$path));
    }

    private function lineEncoding($content)
    {
        return str_replace("\r\n", "\n", $content);
    }

    private function app_path($path)
    {
        return $this->root->url().'/app/'.$path;
    }
}
