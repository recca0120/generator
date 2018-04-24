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
        $config = Arr::get($this->config, 'commands.'.$command);
        $className = $this->name.Arr::get($config, 'suffix', '');
        $attributes = Arr::get($config, 'attributes', []);
        $useSort = Arr::get($config, 'sort', true);

        return $this->format(
            strtr($this->files->get($config['stub']), $this->toDummy(array_merge($attributes, [
                'name' => $this->name,
                'class' => $className,
            ]))),
            $useSort
        );
    }

    private function toDummy($attributes)
    {
        $dummy = [];

        if (empty($attributes['extends']) === false) {
            $attributes['base_extends'] = basename($attributes['extends']);
        }

        foreach ($attributes as $key => $value) {
            $dummy['Dummy'.Str::studly($key)] = $value;
            $dummy['dummy'.Str::studly($key)] = Str::camel($value);
        }

        return $dummy;
    }

    private function format($content, $useSort = false)
    {
        return $useSort === true ? $this->useSortFixer->fix($content) : $content;
    }
}
