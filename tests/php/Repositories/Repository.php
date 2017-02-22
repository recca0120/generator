<?php

namespace App\Repositories;

use App\UserProvider;
use Recca0120\Repository\EloquentRepository;
use App\Repositories\Contracts\UserProviderRepository as UserProviderRepositoryContract;

class UserProviderRepository extends EloquentRepository implements UserProviderRepositoryContract
{
    public function __construct(UserProvider $userProvider)
    {
        $this->model = $userProvider;
    }
}
