<?php

namespace App\Repositories;

use App\FooBarWithoutExtend;
use App\Repositories\Contracts\FooBarWithoutExtendRepository as FooBarWithoutExtendRepositoryContract;

class FooBarWithoutExtendRepository implements FooBarWithoutExtendRepositoryContract
{
    /**
     * Create a new repository instance.
     *
     * @param \App\FooBarWithoutExtend $fooBarWithoutExtend
     */
    public function __construct(FooBarWithoutExtend $fooBarWithoutExtend)
    {
        $this->model = $fooBarWithoutExtend;
    }
}
