<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserProviderRepository as UserProviderRepositoryContract;
use App\UserProvider;
use Recca0120\Repository\EloquentRepository;

class Repository extends EloquentRepository implements UserProviderRepositoryContract
{
    public function __construct(UserProvider $userProvider)
    {
        $this->model = $userProvider;
    }
}
