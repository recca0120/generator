<?php

namespace Recca0120\Generator\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\Generator\Generator;

class GeneratorTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testGenerator()
    {
        new Generator();
    }
}
