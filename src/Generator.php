<?php

namespace Recca0120\Generator;

use Illuminate\Filesystem\Filesystem;
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


        $baseClass = basename($value);
        $namespace = rtrim(preg_replace('/'.$baseClass.'$/', '', $value), '\\');
        $singular = Str::singular(preg_replace('/(Controller|Repository)$/', '', lcfirst($baseClass)));
        $singularSnake = Str::snake($singular);
        $plural = Str::plural($singular);
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
                    ->set('DummyFullPresenterClass', $namespace.'\Presenters\\'.$baseClass.'Presenter')
                    ->setDefault('DummyPresenterClass', $baseClass.'Presenter');;
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

    protected function setDefault($key, $value) {
        if (isset($this->attributes[$key]) === false) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function render($stub)
    {
        $stub = __DIR__.'/../resources/stubs/'.$stub.'.stub';

        return strtr($this->filesystem->get($stub), $this->attributes);
    }
}
