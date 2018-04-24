<?php

namespace Recca0120\Generator;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class Code
{
    public $storePath = '';
    private $content;

    private $attributes = [];

    private $dependencies = [];

    private $files;

    public function __construct($content, $attributes = [], $storePath = '', $dependencies = [], $files = null)
    {
        $this->content = $content;
        $this->attributes = $attributes;
        $this->dependencies = $dependencies;
        $this->storePath = $storePath;
        $this->files = $files ?: new Filesystem;
    }

    public function __toString()
    {
        return $this->content();
    }

    public function getAttributes($prefix = null)
    {
        if ($prefix !== null) {
            $attributes = [];
            foreach ($this->attributes as $key => $value) {
                $attributes[Str::studly($prefix.'_'.$key)] = $value;
            }

            if (empty($this->attributes['namespace']) === false) {
                $attributes[Str::studly($prefix.'_name')] = $this->attributes['namespace'].'\\'.$this->attributes['name'];
            }

            return $attributes;
        }

        return $this->attributes;
    }

    public function content()
    {
        return $this->content;
    }

    public function store()
    {
        foreach ($this->dependencies as $dependency) {
            $this->files->put($dependency->storePath, $dependency->content());
        }

        $this->files->put($this->storePath, $this->content());
    }
}
