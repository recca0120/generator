<?php

namespace Recca0120\Generator\Plugins;

use Illuminate\Filesystem\Filesystem;
use Recca0120\Generator\Fixers\UseSortFixer;

abstract class Plugin
{
    protected $config = [];

    protected $attributes = [];

    protected $files;

    protected $useSortFixer;

    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function setFilesystem(Filesystem $files)
    {
        $this->files = $files;

        return $this;
    }

    public function setUseSortFixer(UseSortFixer $useSortFixer)
    {
        $this->useSortFixer = $useSortFixer;
        $this->useSortFixer->setSortType(UseSortFixer::SORT_TYPE_LENGTH);

        return $this;
    }

    abstract public function process();
}
