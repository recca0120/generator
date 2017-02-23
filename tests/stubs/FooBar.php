<?php

namespace App;

use Plank\Mediable\Mediable;
use App\Presenters\Presentable;
use App\Presenters\FooBarPresenter;
use Illuminate\Database\Eloquent\Model;

class FooBar extends Model
{
    use Mediable;
    use Presentable;

    protected $presenter = FooBarPresenter::class;

    protected $fillable = [];
}
