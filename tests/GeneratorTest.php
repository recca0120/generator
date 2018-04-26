<?php

namespace Recca0120\Generator\Tests;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Generator;

class GeneratorTest extends TestCase
{
    private $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup();

        parent::setUp();
        $this->config = [
            'model' => [
                'path' => $this->base_path('app'),
                'stub' => resource_path('stubs/app/Model.stub'),
                'attributes' => [
                    'namespace' => 'App',
                    'extends' => 'Illuminate\Database\Eloquent\Model',
                ],
            ],
            'repository-contract' => [
                'path' => $this->base_path('app/Repositories/Contracts'),
                'stub' => resource_path('stubs/app/Repositories/Contracts/Repository.stub'),
                'suffix' => 'Repository',
                'sort' => false,
                'attributes' => [
                    'namespace' => 'App\Repositories\Contracts',
                ],
            ],
            'repository' => [
                'path' => $this->base_path('app/Repositories'),
                'stub' => resource_path('stubs/app/Repositories/Repository.stub'),
                'suffix' => 'Repository',
                'attributes' => [
                    'namespace' => 'App\Repositories',
                    'extends' => 'Recca0120\Repository\EloquentRepository',
                ],
                'dependencies' => [
                    'model',
                    'repository-contract',
                ],
            ],
        ];
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
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

    private function getFixture($path)
    {
        return $this->lineEncoding(file_get_contents(__DIR__.'/fixtures/'.$path));
    }

    private function lineEncoding($content)
    {
        return str_replace("\r\n", "\n", $content);
    }

    private function base_path($path)
    {
        return $this->root->url().'/'.$path;
    }
}
