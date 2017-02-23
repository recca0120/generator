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
        $namespace = $this->getNamespace($value);
        $singular = Str::singular(preg_replace('/(Controller|Repository)$/', '', lcfirst($baseClass)));
        $singularSnake = Str::snake($singular);
        $plural = Str::plural($singular);

        $dummyView = Str::snake($plural);
        $dummyRoute = Str::snake($plural);

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
                    ->setDefault('DummyFullRepositoryInterface', $namespace.'\Contracts\\'.$baseClass)
                    ->set('DummyRepositoryClass', $baseClass);
                break;
            case 'DummyFullModelClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyModelVariable', lcfirst($baseClass))
                    ->setDefault('DummyFullPresenterClass', $namespace.'\Presenters\\'.$baseClass.'Presenter')
                    ->setDefault('DummyPresenterClass', $baseClass.'Presenter')
                    ->set('DummyModelClass', $baseClass);
                break;
            case 'DummyFullPresenterClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->set('DummyPresenterClass', $baseClass);
                break;
            case 'DummyFullRequestClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->set('DummyRequestClass', $baseClass);
                break;
            case 'DummyFullControllerClass':
                $this->setDefault('DummyNamespace', $namespace)
                    ->setDefault('DummyClass', $baseClass)
                    ->setDefault('DummyVariable', $plural)
                    ->setDefault('DummyPluralVariable', $plural)
                    ->setDefault('DummyPluralSnakeVariable', $pluralSnake)
                    ->setDefault('DummySingularVariable', $singular)
                    ->setDefault('DummySingularSnakeVariable', $singularSnake)
                    ->setDefault('DummyView', $dummyView)
                    ->setDefault('DummyRoute', $dummyRoute)
                    ->set('DummyControllerClass', $baseClass)
                    ->setDefault('DummyBaseClass', 'Controller');
                break;
            case 'DummyFullBaseClass':
                $this->set('DummyBaseClass', $baseClass);

                if ($this->get('DummyNamespace') === $this->getNamespace($value)) {
                    $this->remove('DummyFullBaseClass');
                }

                break;
        }

        return $this;
    }

    public function get($key) {
        return Arr::get($this->attributes, $key);
    }

    public function remove($key) {
        return Arr::forget($this->attributes, $key);
    }

    public function render($stub)
    {
        return strtr(
            strtr($this->filesystem->get($stub), $this->attributes), [
                ' extends DummyBaseClass' => '',
                "use DummyFullBaseClass;\n" => ''
            ]
        );
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

    protected function getNamespace($name) {
        $baseClass = class_basename($name);

        return rtrim(preg_replace('/'.$baseClass.'$/', '', $name), '\\');
    }

    protected function setDefault($key, $value)
    {
        if (isset($this->attributes[$key]) === false) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }
}
