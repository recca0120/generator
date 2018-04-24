<?php

namespace Recca0120\Generator;

use Illuminate\Support\Str;

class Code
{
    private $content;

    private $attributes = [];

    private $dependencies = [];

    private $config = [];

    public function __construct($content, $attributes = [], $dependencies = [], $config = [])
    {
        $this->content = $content;
        $this->attributes = $attributes;
        $this->config = $config;
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
                $attributes[Str::studly($prefix.'_name')] = $this->attributes['namespace'] . '\\' . $this->attributes['name'];
            }

            return $attributes;
        }

        return $this->attributes;
    }

    public function content()
    {
        return $this->content;
    }

    public function store() {}
}
