<?php

namespace Recca0120\Generator\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Generator;

class GeneratorTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->config = [
            'commands' => [
                'model' => [
                    'path' => base_path('app'),
                    'stub' => resource_path('stubs/app/Model.stub'),
                    'attributes' => [
                        'namespace' => 'App',
                        'extends' => 'Illuminate\Database\Eloquent\Model',
                    ],
                ],
                'repository-contract' => [
                    'path' => base_path('app/Repositories/Contracts'),
                    'stub' => resource_path('stubs/app/Repositories/Contracts/Repository.stub'),
                    'suffix' => 'Repository',
                    'sort' => false,
                    'attributes' => [
                        'namespace' => 'App\Repositories\Contracts',
                    ],
                ],
                'repository' => [
                    'path' => base_path('app/Repositories/Contracts'),
                    'stub' => resource_path('stubs/app/Repositories/Repository.stub'),
                    'suffix' => 'Repository',
                    'attributes' => [
                        'namespace' => 'App\Repositories',
                        'extends' => 'Recca0120\Repository\EloquentRepository',
                    ],
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
        $this->assertSame(
            $this->lineEncoding($generator->setName('FooBar')->render('model')),
            $this->getFixture('app/FooBar.php')
        );
    }

    /** @test */
    public function it_should_generate_repository_contract()
    {
        $generator = new Generator($this->config);
        $this->assertSame(
            $this->lineEncoding($generator->setName('FooBar')->render('repository-contract')),
            $this->getFixture('app/Repositories/Contracts/FooBarRepository.php')
        );
    }

    /** @test */
    public function it_should_generate_repository()
    {
        $generator = new Generator($this->config);
        $this->assertSame(
            $this->lineEncoding($generator->setName('FooBar')->render('repository')),
            $this->getFixture('app/Repositories/FooBarRepository.php')
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
}
