<?php

namespace App\Repositories;

use App\FooBar;
use Recca0120\Repository\EloquentRepository;
use App\Repositories\Contracts\FooBarRepository as FooBarRepositoryContract;

class FooBarRepository extends EloquentRepository implements FooBarRepositoryContract
{
    public function __construct(FooBar $fooBar)
    {
        $this->model = $fooBar;
    }
}
