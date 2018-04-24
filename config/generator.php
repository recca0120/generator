<?php

return [
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
        'dependencies' => [
            'model',
            'repository-contract',
        ],
    ],
];
