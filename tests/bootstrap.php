<?php
/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

use Carbon\Carbon;

if (class_exists('PHPUnit\Framework\TestCase') === false) {
    class_alias('PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase');
}

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
|
| Here we will set the default timezone for PHP. PHP is notoriously mean
| if the timezone is not explicitly set. This will be used by each of
| the PHP date and date-time functions throughout the application.
|
*/

date_default_timezone_set('UTC');

Carbon::setTestNow(Carbon::now());

session_start();

if (function_exists('env') === false) {
    function env($env)
    {
        switch ($env) {
            case 'APP_ENV':
                return 'local';
                break;

            case 'APP_DEBUG':
                return true;
                break;
        }
    }
}

if (function_exists('base_path') === false) {
    function base_path($path)
    {
        return realpath(__DIR__.'/fixtures/'.$path);
    }
}

if (function_exists('app_path') === false) {
    function app_path($path)
    {
        return realpath(__DIR__.'/fixtures/app/'.$path);
    }
}

if (function_exists('resource_path') === false) {
    function resource_path($path)
    {
        return realpath(__DIR__.'/../resources/'.$path);
    }
}
