<?php

namespace Recca0120\Generator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Generator\Fixers\UseSortFixer;

class Code
{
    private $name;

    private $className;

    private $config;

    private $attributes = [];

    private $dependencies = [];

    private $files;

    public function __construct($name, $config, $dependencies = [], Filesystem $files = null, UseSortFixer $useSortFixer = null)
    {
        $this->files = $files ?: new Filesystem;
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer();
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);
        $this->name = $name;
        $this->config = $config;

        $this->className = $this->name.Arr::get($this->config, 'suffix', '');
        $this->attributes = $this->mergeAttributes($dependencies);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function getAttributes($prefix = null)
    {
        if (empty($prefix) === true) {
            return $this->attributes;
        }

        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            $attributes[$prefix.'_'.$key] = $value;
        }

        return $attributes;
    }

    public function render()
    {
        return $this->format($this->renderStub(), Arr::get($this->config, 'sort', true));
    }

    public function store()
    {
        foreach ($this->dependencies as $dependency) {
            $dependency->store();
        }

        $path = Arr::get($this->config, 'path', '').'/'.$this->className.'.'.Arr::get($this->config, 'extension', 'php');
        $code = $this->render();

        return $this->files->put($path, $code);
    }

    private function getDummyAttributes()
    {
        $dummy = [];
        foreach ($this->attributes as $key => $value) {
            $dummy['Dummy'.Str::studly($key)] = $value;
            $dummy['dummy'.Str::studly($key)] = Str::camel($value);
        }

        return $dummy;
    }

    private function renderStub()
    {
        return strtr($this->files->get($this->config['stub']), $this->getDummyAttributes());
    }

    private function format($content, $useSort = false)
    {
        return $useSort === true ? $this->useSortFixer->fix($content) : $content;
    }

    private function mergeAttributes($dependencies)
    {
        $attributes = array_merge(Arr::get($this->config, 'attributes', []), [
            'name' => $this->name,
            'class' => $this->className,
        ]);

        if (empty($attributes['extends']) === false) {
            $attributes['base_extends'] = basename($attributes['extends']);
        }

        if (empty($attributes['namespace']) === false) {
            $attributes['fully_qualified_name'] = '\\'.$attributes['namespace'].'\\'.$attributes['class'];
            $attributes['qualified_name'] = $attributes['namespace'].'\\'.$attributes['class'];
        }

        foreach ($dependencies as $name => $dependency) {
            $attributes = array_merge($attributes, $dependency->getAttributes($name));
        }

        return $attributes;
    }
}
