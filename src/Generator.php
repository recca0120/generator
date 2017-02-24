<?php

namespace Recca0120\Generator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Generator\Fixers\UseSortFixer;

class Generator
{
    protected $filesystem;

    protected $useSortFixer;

    protected $attributes = [];

    public function __construct(Filesystem $filesystem = null, UseSortFixer $useSortFixer = null)
    {
        $this->filesystem = $filesystem ?: new Filesystem();
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer();
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);
    }

    protected function parseAttribute($value)
    {
        $alias = array_map('trim', explode(' as ', $value));
        $className = $alias[0];

        $dummyClass = class_basename(isset($alias[1]) === true ? $alias[1] : $className);
        $dummyNamespace = $this->getNamespace($className);

        $dummyModelVariable = Str::camel(Str::singular(
            preg_replace('/(Controller|Repository)$/', '', $dummyClass)
        ));

        $dummyCollectionVariable = Str::plural($dummyModelVariable);
        $dummyCollectionVariable = $dummyCollectionVariable === $dummyModelVariable ?
            $dummyModelVariable.'Collection' : $dummyCollectionVariable;

        $dummyRepositoryVariable = $dummyCollectionVariable;

        $dummyView = Str::snake(Str::plural($dummyModelVariable));
        $dummyRoute = Str::snake(Str::plural($dummyModelVariable));

        return compact(
            'dummyNamespace',
            'dummyClass',
            'dummyRepositoryVariable',
            'dummyCollectionVariable',
            'dummyModelVariable',
            'dummyView',
            'dummyRoute'
        );
    }

    public function setFullBaseClass($value)
    {
        $this->set('DummyFullBaseClass', $value);

        extract($this->parseAttribute($value));

        $this->set('DummyBaseClass', $dummyClass);
        if ($this->get('DummyNamespace') === $this->getNamespace($value)) {
            $this->remove('DummyFullBaseClass');
        }

        return $this;
    }

    public function setFullRepositoryInterface($value)
    {
        $this->set('DummyFullRepositoryInterface', $value);

        extract($this->parseAttribute($value));

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyRepositoryInterface', $dummyClass);
    }

    public function setFullRepositoryClass($value)
    {
        $this->set('DummyFullRepositoryClass', $value);

        extract($this->parseAttribute($value));

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyFullRepositoryInterface', $dummyNamespace.'\Contracts\\'.$dummyClass)
            ->set('DummyRepositoryClass', $dummyClass);
    }

    public function setFullModelClass($value)
    {
        $this->set('DummyFullModelClass', $value);

        extract($this->parseAttribute($value));

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyModelVariable', $dummyModelVariable)
            ->setDefault('DummyFullPresenterClass', $dummyNamespace.'\Presenters\\'.$dummyClass.'Presenter')
            ->setDefault('DummyPresenterClass', $dummyClass.'Presenter')
            ->set('DummyModelClass', $dummyClass);
    }

    public function setFullPresenterClass($value)
    {
        $this->set('DummyFullPresenterClass', $value);

        extract($this->parseAttribute($value));

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->set('DummyPresenterClass', $dummyClass);
    }

    public function setFullRequestClass($value)
    {
        $this->set('DummyFullRequestClass', $value);

        extract($this->parseAttribute($value));

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->set('DummyRequestClass', $dummyClass);
    }

    public function setFullControllerClass($value)
    {
        $this->set('DummyFullControllerClass', $value);

        extract($this->parseAttribute($value));

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyRepositoryVariable', $dummyRepositoryVariable)
            ->setDefault('DummyCollectionVariable', $dummyRepositoryVariable)
            ->setDefault('DummyModelVariable', $dummyModelVariable)
            ->setDefault('DummyView', $dummyView)
            ->setDefault('DummyRoute', $dummyRoute)
            ->set('DummyControllerClass', $dummyClass);
    }

    public function set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    protected function setDefault($key, $value)
    {
        if (isset($this->attributes[$key]) === false) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function get($key)
    {
        return Arr::get($this->attributes, $key);
    }

    public function remove($key)
    {
        return Arr::forget($this->attributes, $key);
    }

    public function render($stub, $orderedUses = true)
    {
        $content = strtr(strtr(strtr($this->filesystem->get($stub), $this->attributes), ["\r\n" => "\n"]), [
            ' extends DummyBaseClass' => '',
            'use DummyFullBaseClass;' => '',
        ]);

        return $orderedUses === true ? $this->orderedUses($content) : $content;
    }

    public function renderServiceProvider($content)
    {
        if (strpos($content, '$this->registerRepositories') === false) {
            $content = preg_replace_callback('/public function register\(.+\n\s+{/', function ($m) {
                return $m[0]."\n".
                    str_repeat(' ', 8).
                    '$this->registerRepositories();';
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

    protected function replaceServieProviderCallback($m)
    {
        $fullRepositoryClass = $this->get('DummyFullRepositoryClass');
        $fullRepositoryInterface = $this->get('DummyFullRepositoryInterface');
        $dummyClass = $this->get('DummyClass');

        if (Str::startsWith($m[0], 'namespace') === true) {
            return $m[0]."\n\n".
                sprintf("use %s as %sContract;\n", $fullRepositoryInterface, $dummyClass).
                sprintf("use %s;\n", $fullRepositoryClass);
        } else {
            return $m[0]."\n".
                str_repeat(' ', 8).
                sprintf('$this->app->singleton(%sContract::class, %s::class);', $dummyClass, $dummyClass);
        }
    }

    protected function getNamespace($name)
    {
        $baseClass = class_basename($name);

        return rtrim(preg_replace('/'.$baseClass.'$/', '', $name), '\\');
    }

    protected function orderedUses($content)
    {
        $fix = $this->useSortFixer->fix($content);

        return $fix === false ? $content : strtr($fix, ["\r\n" => "\n"]);
    }
}
