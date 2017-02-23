<?php

namespace App;

use Plank\Mediable\Mediable;
use App\Presenters\FooBarPresenter;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Model;

class FooBar extends Model
{
    use Mediable;
    use Presentable;

    protected $presenter = FooBarPresenter::class;

    protected $fillable = [];
}
