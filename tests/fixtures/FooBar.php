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

    /**
     * The attribute that point the presenter.
     *
     * @var string
     */
    protected $presenter = FooBarPresenter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
}
