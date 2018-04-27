<?php

return [
    'model' => [
        'path' => app_path(''),
        'stub' => resource_path('stubs/app/Model.stub'),
        'attributes' => [
            'namespace' => 'App',
            'extends_qualified_class' => Illuminate\Database\Eloquent\Model::class,
            'extends_class' => 'Model',
        ],
    ],
    'repository-contract' => [
        'path' => app_path('Repositories/Contracts'),
        'stub' => resource_path('stubs/app/Repositories/Contracts/Repository.stub'),
        'suffix' => 'Repository',
        'attributes' => [
            'namespace' => 'App\Repositories\Contracts',
        ],
    ],
    'repository' => [
        'path' => app_path('Repositories'),
        'stub' => resource_path('stubs/app/Repositories/Repository.stub'),
        'suffix' => 'Repository',
        'attributes' => [
            'namespace' => 'App\Repositories',
            'extends_qualified_class' => \Recca0120\Repository\EloquentRepository::class,
            'extends_class' => 'EloquentRepository',
        ],
        'dependencies' => [
            'model',
            'repository-contract',
        ],
        'plugins' => [
            \Recca0120\Generator\Plugins\ServiceProviderRegister::class => [
                'path' => app_path('Providers/AppServiceProvider.php'),
            ],
        ],
    ],
];
