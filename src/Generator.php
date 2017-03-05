<?php

namespace Recca0120\Generator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Generator\Fixers\UseSortFixer;

class Generator
{
    /**
     * $filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * $useSortFixer.
     *
     * @var \Recca0120\Generator\Fixers\UseSortFixer
     */
    protected $useSortFixer;

    /**
     * $attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * __construct.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem   [description]
     * @param \Recca0120\Generator\Fixers\UseSortFixers $useSortFixer [description]
     */
    public function __construct(Filesystem $filesystem = null, UseSortFixer $useSortFixer = null)
    {
        $this->filesystem = $filesystem ?: new Filesystem();
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer();
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);
    }

    /**
     * parseAttribute.
     *
     * @param string $value
     * @return array
     */
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

    /**
     * setFullBaseClass.
     *
     * @param string $value
     * @return static
     */
    public function setFullBaseClass($value)
    {
        $this->set('DummyFullBaseClass', $value);
        $attributes = $this->parseAttribute($value);
        extract($attributes);

        $this->set('DummyBaseClass', $dummyClass);
        if ($this->get('DummyNamespace') === $this->getNamespace($value)) {
            $this->remove('DummyFullBaseClass');
        }

        return $this;
    }

    /**
     * setFullRepositoryInterface.
     *
     * @param string $value
     * @return static
     */
    public function setFullRepositoryInterface($value)
    {
        $this->set('DummyFullRepositoryInterface', $value);
        $attributes = $this->parseAttribute($value);
        extract($attributes);

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyRepositoryInterface', $dummyClass);
    }

    /**
     * setFullRepositoryClass.
     *
     * @param string $value
     * @return static
     */
    public function setFullRepositoryClass($value)
    {
        $this->set('DummyFullRepositoryClass', $value);
        $attributes = $this->parseAttribute($value);
        extract($attributes);

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyFullRepositoryInterface', $dummyNamespace.'\Contracts\\'.$dummyClass)
            ->set('DummyRepositoryClass', $dummyClass);
    }

    /**
     * setFullModelClass.
     *
     * @param string $value
     * @return static
     */
    public function setFullModelClass($value)
    {
        $this->set('DummyFullModelClass', $value);
        $attributes = $this->parseAttribute($value);
        extract($attributes);

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyModelVariable', $dummyModelVariable)
            ->setDefault('DummyFullPresenterClass', $dummyNamespace.'\Presenters\\'.$dummyClass.'Presenter')
            ->setDefault('DummyPresenterClass', $dummyClass.'Presenter')
            ->set('DummyModelClass', $dummyClass);
    }

    /**
     * setFullPresenterClass.
     *
     * @param string $value
     * @return static
     */
    public function setFullPresenterClass($value)
    {
        $this->set('DummyFullPresenterClass', $value);
        $attributes = $this->parseAttribute($value);
        extract($attributes);

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->set('DummyPresenterClass', $dummyClass);
    }

    /**
     * setFullRequestClass.
     *
     * @param string $value
     * @return static
     */
    public function setFullRequestClass($value)
    {
        $this->set('DummyFullRequestClass', $value);
        $attributes = $this->parseAttribute($value);
        extract($attributes);

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->set('DummyRequestClass', $dummyClass);
    }

    /**
     * setFullControllerClass.
     *
     * @param string $value
     * @return static
     */
    public function setFullControllerClass($value)
    {
        $this->set('DummyFullControllerClass', $value);
        $attributes = $this->parseAttribute($value);
        extract($attributes);

        return $this->setDefault('DummyNamespace', $dummyNamespace)
            ->setDefault('DummyClass', $dummyClass)
            ->setDefault('DummyRepositoryVariable', $dummyRepositoryVariable)
            ->setDefault('DummyCollectionVariable', $dummyCollectionVariable)
            ->setDefault('DummyModelVariable', $dummyModelVariable)
            ->setDefault('DummyView', $dummyView)
            ->setDefault('DummyRoute', $dummyRoute)
            ->set('DummyControllerClass', $dummyClass);
    }

    /**
     * set.
     *
     * @param string $value
     * @return static
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * setDefault.
     *
     * @param string $value
     * @return static
     */
    protected function setDefault($key, $value)
    {
        if (isset($this->attributes[$key]) === false) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * get.
     *
     * @return string
     */
    public function get($key)
    {
        return Arr::get($this->attributes, $key);
    }

    /**
     * remove.
     *
     * @param string $key
     * @return bool
     */
    public function remove($key)
    {
        return Arr::forget($this->attributes, $key);
    }

    /**
     * render.
     *
     * @param string $stub
     * @param bool $orderedUses
     * @return string
     */
    public function render($stub, $orderedUses = true)
    {
        $content = strtr(strtr(strtr($this->filesystem->get($stub), $this->attributes), ["\r\n" => "\n"]), [
            ' extends DummyBaseClass' => '',
            'use DummyFullBaseClass;' => '',
        ]);

        return $orderedUses === true ? $this->orderedUses($content) : $content;
    }

    /**
     * renderServiceProvider.
     *
     * @param string $content
     * @return string
     */
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

    /**
     * replaceServieProviderCallback.
     *
     * @param  array $match
     * @return string
     */
    protected function replaceServieProviderCallback($match)
    {
        $fullRepositoryClass = $this->get('DummyFullRepositoryClass');
        $fullRepositoryInterface = $this->get('DummyFullRepositoryInterface');
        $dummyClass = $this->get('DummyClass');

        if (Str::startsWith($match[0], 'namespace') === true) {
            return $match[0]."\n\n".
                sprintf("use %s as %sContract;\n", $fullRepositoryInterface, $dummyClass).
                sprintf("use %s;\n", $fullRepositoryClass);
        } else {
            return $match[0]."\n".
                str_repeat(' ', 8).
                sprintf('$this->app->singleton(%sContract::class, %s::class);', $dummyClass, $dummyClass);
        }
    }

    /**
     * getNamespace.
     *
     * @param string $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return rtrim(preg_replace('/'.class_basename($name).'$/', '', $name), '\\');
    }


    /**
     * orderedUses.
     *
     * @param string $content
     * @return string
     */
    protected function orderedUses($content)
    {
        $fix = $this->useSortFixer->fix($content);

        return $fix === false ? $content : strtr($fix, ["\r\n" => "\n"]);
    }
}
