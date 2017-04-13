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
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @param \Recca0120\Generator\Fixers\UseSortFixer $useSortFixer
     */
    public function __construct(Filesystem $filesystem = null, UseSortFixer $useSortFixer = null)
    {
        $this->filesystem = $filesystem ?: new Filesystem();
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer();
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);
    }

    /**
     * setFullBaseClass.
     *
     * @param string $className
     * @return $this
     */
    public function setFullBaseClass($className)
    {
        $attributes = $this->parseAttribute($className);

        $this->set('DummyFullBaseClass', $className)
            ->set('DummyBaseClass', $attributes['DummyClass']);

        if ($this->get('DummyNamespace') === $this->getNamespace($className)) {
            $this->remove('DummyFullBaseClass');
        }

        return $this;
    }

    /**
     * setFullRepositoryInterface.
     *
     * @param string $className
     * @return $this
     */
    public function setFullRepositoryInterface($className)
    {
        $attributes = $this->parseAttribute($className);

        return $this->set('DummyFullRepositoryInterface', $className)
            ->set('DummyNamespace', $attributes['DummyNamespace'], false)
            ->set('DummyClass', $attributes['DummyClass'], false)
            ->set('DummyRepositoryInterface', $attributes['DummyClass'], false);
    }

    /**
     * setFullRepositoryClass.
     *
     * @param string $className
     * @return $this
     */
    public function setFullRepositoryClass($className)
    {
        $attributes = $this->parseAttribute($className);

        return $this->set('DummyFullRepositoryClass', $className)
            ->set('DummyNamespace', $attributes['DummyNamespace'], false)
            ->set('DummyClass', $attributes['DummyClass'], false)
            ->set('DummyFullRepositoryInterface', $attributes['DummyNamespace'].'\Contracts\\'.$attributes['DummyClass'], false)
            ->set('DummyRepositoryClass', $attributes['DummyClass']);
    }

    /**
     * setFullModelClass.
     *
     * @param string $className
     * @return $this
     */
    public function setFullModelClass($className)
    {
        $attributes = $this->parseAttribute($className);

        return $this->set('DummyFullModelClass', $className)
            ->set('DummyNamespace', $attributes['DummyNamespace'], false)
            ->set('DummyClass', $attributes['DummyClass'], false)
            ->set('dummyModel', $attributes['dummyModel'], false)
            ->set('DummyFullPresenterClass', $attributes['DummyNamespace'].'\Presenters\\'.$attributes['DummyClass'].'Presenter', false)
            ->set('DummyPresenterClass', $attributes['DummyClass'].'Presenter', false)
            ->set('DummyModelClass', $attributes['DummyClass']);
    }

    /**
     * setFullPresenterClass.
     *
     * @param string $className
     * @return $this
     */
    public function setFullPresenterClass($className)
    {
        $attributes = $this->parseAttribute($className);

        return $this->set('DummyFullPresenterClass', $className)
            ->set('DummyNamespace', $attributes['DummyNamespace'], false)
            ->set('DummyClass', $attributes['DummyClass'], false)
            ->set('DummyPresenterClass', $attributes['DummyClass']);
    }

    /**
     * setFullRequestClass.
     *
     * @param string $className
     * @return $this
     */
    public function setFullRequestClass($className)
    {
        $attributes = $this->parseAttribute($className);

        return $this->set('DummyFullRequestClass', $className)
            ->set('DummyNamespace', $attributes['DummyNamespace'], false)
            ->set('DummyClass', $attributes['DummyClass'], false)
            ->set('DummyRequestClass', $attributes['DummyClass']);
    }

    /**
     * setFullControllerClass.
     *
     * @param string $className
     * @return $this
     */
    public function setFullControllerClass($className)
    {
        $attributes = $this->parseAttribute($className);

        return $this->set('DummyFullControllerClass', $className)
            ->set('DummyNamespace', $attributes['DummyNamespace'], false)
            ->set('DummyClass', $attributes['DummyClass'], false)
            ->set('dummyRepository', $attributes['dummyRepository'], false)
            ->set('dummyCollection', $attributes['dummyCollection'], false)
            ->set('dummyModel', $attributes['dummyModel'], false)
            ->set('dummyView', $attributes['dummyView'], false)
            ->set('dummyRoute', $attributes['dummyRoute'], false)
            ->set('dummyTitle', $attributes['dummyTitle'], false)
            ->set('DummyControllerClass', $attributes['DummyClass']);
    }

    /**
     * set.
     *
     * @param string $value
     * @return $this
     */
    public function set($key, $value = null, $replace = true)
    {
        if ($replace === false && isset($this->attributes[$key]) === true) {
            return $this;
        }
        $this->attributes[$key] = $value;

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
     * @param bool $orderedUse
     * @return string
     */
    public function render($stub, $orderedUse = true)
    {
        $content = strtr(
            strtr($this->filesystem->get($stub), $this->attributes), [
                ' extends DummyBaseClass' => '',
                'use DummyFullBaseClass;' => '',
            ]);

        return $this->format($content, $orderedUse);
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

        return $this->format($content);
    }

    /**
     * parseAttribute.
     *
     * @param string $className
     * @return array
     */
    protected function parseAttribute($className)
    {
        $alias = array_map('trim', explode(' as ', $className));
        $className = $alias[0];

        $dummyClass = class_basename(isset($alias[1]) === true ? $alias[1] : $className);
        $dummyNamespace = $this->getNamespace($className);

        $singular = Str::camel(Str::singular(
            preg_replace('/(Controller|Repository)$/', '', $dummyClass)
        ));
        $plural = Str::plural($singular);

        $dummyModel = $singular;
        $dummyRepository = $plural;
        $dummyCollection = $singular === $plural ? $singular.'Collection' : $plural;
        $dummyView = Str::snake($plural, '-');
        $dummyRoute = Str::snake($plural, '-');
        $dummyTitle = ucwords(Str::snake($plural, ' '));

        $pos = strpos($dummyNamespace, 'Controller');
        if ($pos !== false) {
            $prefix = Str::camel(trim(substr($dummyNamespace, $pos + 12), '\\'));
            if (empty($prefix) === false) {
                $dummyView = $prefix.'::'.$dummyView;
                $dummyRoute = $prefix.'.'.$dummyRoute;
            }
        }

        return [
            'DummyNamespace' => $dummyNamespace,
            'DummyClass' => $dummyClass,
            'DummyModelClass' => $dummyClass,
            'dummyModel' => $dummyModel,
            'dummyRepository' => $dummyRepository,
            'dummyCollection' => $dummyCollection,
            'dummyView' => $dummyView,
            'dummyRoute' => $dummyRoute,
            'dummyTitle' => $dummyTitle,
        ];
    }

    /**
     * replaceServieProviderCallback.
     *
     * @param array $match
     * @return string
     */
    protected function replaceServieProviderCallback($match)
    {
        $fullRepositoryClass = $this->get('DummyFullRepositoryClass');
        $fullRepositoryInterface = $this->get('DummyFullRepositoryInterface');
        $DummyClass = $this->get('DummyClass');

        if (Str::startsWith($match[0], 'namespace') === true) {
            return $match[0]."\n\n".
                sprintf("use %s as %sContract;\n", $fullRepositoryInterface, $DummyClass).
                sprintf("use %s;\n", $fullRepositoryClass);
        } else {
            return $match[0]."\n".
                str_repeat(' ', 8).
                sprintf('$this->app->singleton(%sContract::class, %s::class);', $DummyClass, $DummyClass);
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
     * format.
     *
     * @param string $content
     * @param bool $orderedUse
     * @return string
     */
    protected function format($content, $orderedUse = true)
    {
        if ($orderedUse === true && ($ordered = $this->useSortFixer->fix($content)) !== false) {
            $content = $ordered;
        }

        return strtr($content, ["\r\n" => "\n"]);
    }
}
