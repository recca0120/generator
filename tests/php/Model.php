<?php

namespace App;

use Plank\Mediable\Mediable;
use App\Presenters\UserProviderPresenter;
use App\Presenters\Presentable;
use Illuminate\Database\Eloquent\Model;

class UserProvider extends Model
{
    use Mediable;
    use Presentable;

    protected $presenter = UserProviderPresenter::class;

    protected $fillable = [];
}
