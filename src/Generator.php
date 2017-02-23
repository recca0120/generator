<?php

namespace Recca0120\Generator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class Generator
{
    protected $filesystem;

    protected $useSortFixer;

    protected $attributes = [];

    public function __construct(Filesystem $filesystem = null, UseSortFixer $useSortFixer = null)
    {
        $this->filesystem = $filesystem ?: new Filesystem;
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer;
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);
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

    protected function setDefault($key, $value)
    {
        if (isset($this->attributes[$key]) === false) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function get($key) {
        return Arr::get($this->attributes, $key);
    }

    public function remove($key) {
        return Arr::forget($this->attributes, $key);
    }

    public function render($stub, $orderedUses = true)
    {
        $content = strtr(
            strtr($this->filesystem->get($stub), $this->attributes), [
                ' extends DummyBaseClass' => '',
                "use DummyFullBaseClass;\n" => ''
            ]
        );

        return $orderedUses === true ? $this->orderedUses($content) : $content;
    }

    public function renderServiceProvider($content)
    {
        if (strpos($content, '$this->registerRepositories') === false) {
            $content = preg_replace_callback('/public function register\(.+\n\s+{/', function ($m) {
                return $m[0]."\n".
                    str_repeat(' ', 8).
                    "\$this->registerRepositories();\n";
            }, $content);
        }

        if (strpos($content, 'protected function registerRepositories()') === false) {
            $content = substr($content, 0, strrpos($content, '}')).
                "\n".str_repeat(' ', 4).
                'protected function registerRepositories()'.
                "\n".str_repeat(' ', 4).'{'.
                "\n".str_repeat(' ', 4).'}'.
                "\n}\n";
        }

        if (strpos($content, sprintf('use %s;', $this->get('DummyFullRepositoryClass'))) === false) {
            $content = preg_replace_callback(
                '/namespace.+/',
                [$this, 'replaceServieProviderCallback'],
                $content
            );
        }

        if (strpos($content, sprintf('$this->app->singleton(%sContract::class, %s::class);', $this->get('DummyClass'), $this->get('DummyClass'))) === false) {
            $content = preg_replace_callback(
                '/protected function registerRepositories.+\n\s+{/',
                [$this, 'replaceServieProviderCallback'],
                $content
            );
        }

        return $this->orderedUses($content);
    }

    protected function replaceServieProviderCallback($m) {
        $fullRepositoryClass = $this->get('DummyFullRepositoryClass');
        $fullRepositoryInterface = $this->get('DummyFullRepositoryInterface');
        $dummyClass = $this->get('DummyClass');

        if (Str::startsWith($m[0], 'namespace') === true) {
            return $m[0]."\n\n".
                sprintf("use %s as %sContract;\n", $fullRepositoryInterface,  $dummyClass).
                sprintf("use %s;\n", $fullRepositoryClass);
        } else {
            return $m[0]."\n".
                str_repeat(' ', 8).
                sprintf('$this->app->singleton(%sContract::class, %s::class);', $dummyClass, $dummyClass);
        }
    }

    protected function getNamespace($name) {
        $baseClass = class_basename($name);

        return rtrim(preg_replace('/'.$baseClass.'$/', '', $name), '\\');
    }

    protected function orderedUses($content) {
        $fix = $this->useSortFixer->fix($content);

        return $fix === false ? $content : strtr($fix, ["\r\n" => "\n"]);
    }
}
