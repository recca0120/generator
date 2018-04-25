<?php

namespace App\Repositories;

use App\FooBar;
use Recca0120\Repository\EloquentRepository;
use App\Repositories\Contracts\FooBarRepository as FooBarRepositoryContract;

class FooBarRepository extends EloquentRepository implements FooBarRepositoryContract
{
    /**
     * Create a new repository instance.
     *
     * @param \App\FooBar $fooBar
     */
    public function __construct(FooBar $fooBar)
    {
        parent::__construct($fooBar);
    }
}
