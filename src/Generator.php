<?php

namespace Recca0120\Generator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Generator
{
    protected $filesystem;

    protected $attributes = [
    ];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function set($key, $value)
    {
        $this->attributes[$key] = $value;

        $baseClass = class_basename($value);
        $namespace = rtrim(preg_replace('/'.$baseClass.'$/', '', $value), '\\');
        $singular = Str::singular(preg_replace('/(Controller|Repository)$/', '', lcfirst($baseClass)));
        $singularSnake = Str::snake($singular);
        $plural = Str::plural($singular);
        $plural = $singular === $plural ? $singular.'Collection' : $plural;
        $pluralSnake = Str::snake($plural);

        switch ($key) {
            case 'DummyFullRepositoryInterface':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyRepositoryInterface', $baseClass);
                break;
            case 'DummyFullRepositoryClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyRepositoryClass', $baseClass)
                    ->setDefault('DummyFullRepositoryInterface', $namespace.'\Contracts\\'.$baseClass);
                break;
            case 'DummyFullModelClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyModelClass', $baseClass)
                    ->setDefault('DummyModelVariable', lcfirst($baseClass))
                    ->setDefault('DummyFullPresenterClass', $namespace.'\Presenters\\'.$baseClass.'Presenter')
                    ->setDefault('DummyPresenterClass', $baseClass.'Presenter');
                break;
            case 'DummyFullPresenterClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyPresenterClass', $baseClass);
                break;
            case 'DummyFullRequestClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyRequestClass', $baseClass);
                break;
            case 'DummyFullControllerClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyControllerClass', $baseClass)
                    ->setDefault('DummyVariable', $plural)
                    ->setDefault('DummyPluralVariable', $plural)
                    ->setDefault('DummyPluralSnakeVariable', $pluralSnake)
                    ->setDefault('DummySingularVariable', $singular)
                    ->setDefault('DummySingularSnakeVariable', $singularSnake);
                break;
        }

        return $this;
    }

    public function get($key)
    {
        return Arr::get($this->attributes, $key);
    }

    public function registerServiceProvider($content)
    {
        $fullRepositoryClass = $this->get('DummyFullRepositoryClass');
        $fullRepositoryInterface = $this->get('DummyFullRepositoryInterface');
        $dummyClass = $this->get('DummyClass');

        if (strpos($content, 'registerRepositories') === false) {
            $content = preg_replace_callback('/public function register\(.+\n\s+{/', function ($m) {
                return $m[0]."\n\n".
                    str_repeat(' ', 8).
                    "\$this->registerRepositories();\n";
            }, $content);

            $content = substr($content, 0, strrpos($content, '}')).
                "\n".str_repeat(' ', 4).
                'protected function registerRepositories()'.
                "\n".str_repeat(' ', 4).'{'.
                "\n".str_repeat(' ', 4).'}'.
                "\n}\n";
        }

        if (strpos($content, $fullRepositoryClass) === false) {
            $content = preg_replace_callback('/namespace.+|protected function registerRepositories.+\n\s+{/', function ($m) use ($fullRepositoryClass, $fullRepositoryInterface, $dummyClass) {
                if (Str::startsWith($m[0], 'namespace') === true) {
                    return $m[0]."\n\n".
                        sprintf("use %s as %sContract;\n", $fullRepositoryInterface,  $dummyClass).
                        sprintf("use %s;\n", $fullRepositoryClass);
                } else {
                    return $m[0]."\n".
                        str_repeat(' ', 8).
                        sprintf('$this->app->singleton(%sContract::class, %s::class);', $dummyClass, $dummyClass);
                }
            }, $content);
        }

        return $content;
    }

    protected function setDefault($key, $value)
    {
        if (isset($this->attributes[$key]) === false) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function render($stub)
    {
        return strtr($this->filesystem->get($stub), $this->attributes);
    }
}
