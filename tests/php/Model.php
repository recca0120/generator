<?php

namespace App;

use App\Presenters\Presentable;
use App\Presenters\UserProviderPresenter;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class UserProvider extends Model
{
    use Mediable;
    use Presentable;

    protected $presenter = UserProviderPresenter::class;

    protected $fillable = [];
}
