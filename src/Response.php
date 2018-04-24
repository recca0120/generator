<?php

namespace Recca0120\Generator;

use Illuminate\Support\Str;

class Response
{
    private $content;

    private $attributes = [];

    public function __construct($content, $attributes = [])
    {
        $this->content = $content;
        $this->attributes = $attributes;
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

            return $attributes;
        }

        return $this->attributes;
    }

    public function content()
    {
        return $this->content;
    }
}
