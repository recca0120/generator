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

    private $useSortFixer;

    public function __construct($name, $config, $dependencies = [], Filesystem $files = null, UseSortFixer $useSortFixer = null)
    {
        $this->files = $files ?: new Filesystem;
        $this->useSortFixer = $useSortFixer ?: new UseSortFixer();
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);
        $this->name = $name;
        $this->config = $config;
        $this->dependencies = $dependencies;

        $this->className = $this->name.Arr::get($this->config, 'suffix', '');
        $this->attributes = $this->mergeAttributes(
            $dependencies,
            Arr::get($this->config, 'plugins', [])
        );
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
            $attributes[str_replace('-', '_', $prefix.'_'.$key)] = $value;
        }

        return $attributes;
    }

    public function render()
    {
        return $this->format($this->renderStub());
    }

    public function store()
    {
        foreach ($this->dependencies as $dependency) {
            $dependency->store();
        }

        $file = Arr::get($this->config, 'path', '').'/'.$this->className.'.'.Arr::get($this->config, 'extension', 'php');
        $directory = dirname($file);

        if ($this->files->isDirectory($directory) === false) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        return $this->files->exists($file) === false
            ? $this->files->put($file, $this->render())
            : false;
    }

    private function renderStub()
    {
        return strtr($this->files->get($this->config['stub']), $this->getDummyAttributes());
    }

    private function format($content)
    {
        $fixedContent = $this->useSortFixer->fix($content);

        return $fixedContent ? $fixedContent : $content;
    }

    private function mergeAttributes($dependencies, $plugins)
    {
        $attributes = array_merge(Arr::get($this->config, 'attributes', []), [
            'name' => $this->name,
            'class' => $this->className,
        ]);

        foreach ($attributes as $key => $value) {
            if (Str::endsWith($key, 'qualified_class') === false) {
                continue;
            }

            $key = str_replace('qualified_', '', $key);

            if (empty($attributes[$key]) === false) {
                continue;
            }

            $attributes[$key] = basename(str_replace('//', '/', $value));
        }

        if (empty($attributes['namespace']) === false) {
            $attributes['fully_qualified_class'] = '\\'.$attributes['namespace'].'\\'.$attributes['class'];
            $attributes['qualified_class'] = $attributes['namespace'].'\\'.$attributes['class'];
        }

        foreach ($dependencies as $name => $dependency) {
            $attributes = array_merge($attributes, $dependency->getAttributes($name));
        }

        foreach ($plugins as $pluginClass => $pluginConfig) {
            $plugin = new $pluginClass();
            $plugin->setConfig($pluginConfig);
            $plugin->setAttributes($attributes);
            $plugin->setFilesystem($this->files);
            $plugin->setUseSortFixer($this->useSortFixer);
            $processedAttributes = $plugin->process();
            if (is_array($processedAttributes) === true) {
                $attributes = array_merge($attributes, $processedAttributes);
            }
        }

        return $attributes;
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
}
