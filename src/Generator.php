<?php

namespace Recca0120\Generator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Recca0120\Generator\Fixers\UseSortFixer;

class Generator
{
    private $config;

    private $files;

    private $name = '';

    private $command = '';

    private $attributes = [];

    public function __construct($config, $files = null, UseSortFixer $useSortFixer = null)
    {
        $this->config = $config;
        $this->files = $files ?: new Filesystem();
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer();
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function render($command)
    {
        $config = Arr::get($this->config, $command);
        $className = $this->name.Arr::get($config, 'suffix', '');

        $dependencies = $this->renderDependencies(Arr::get($config, 'dependencies', []));

        $attributes = $this->mergeAttributes(array_merge(Arr::get($config, 'attributes', []), [
            'name' => $this->name,
            'class' => $className,
        ]), $dependencies);

        return new Code(
            $this->format(
                $this->renderStub($config['stub'], $attributes),
                Arr::get($config, 'sort', true)
            ),
            $attributes,
            $config['path'].'/'.$className.'.php',
            $dependencies
        );
    }

    private function renderDependencies($dependencies)
    {
        $codes = [];
        foreach ($dependencies as $dependency) {
            $generator = new static($this->config);
            $codes[$dependency] = $generator
                ->setName($this->name)
                ->render($dependency);
        }

        return $codes;
    }

    private function renderStub($stub, $attributes)
    {
        return strtr($this->files->get($config['stub']), $this->toDummy($attributes));
    }

    private function toDummy($attributes)
    {
        $dummy = [];

        foreach ($attributes as $key => $value) {
            $dummy['Dummy'.Str::studly($key)] = $value;
            $dummy['dummy'.Str::studly($key)] = Str::camel($value);
        }

        return $dummy;
    }

    private function mergeAttributes($attributes, $dependencies)
    {
        if (empty($attributes['extends']) === false) {
            $attributes['base_extends'] = basename($attributes['extends']);
        }

        foreach ($dependencies as $name => $dependency) {
            $attributes = array_merge($attributes, $dependency->getAttributes($name));
        }

        return $attributes;
    }

    private function format($content, $useSort = false)
    {
        return $useSort === true ? $this->useSortFixer->fix($content) : $content;
    }
}
