<?php

namespace Recca0120\Generator\Plugins;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ServiceProviderRegister extends Plugin
{
    public function process()
    {
        $path = Arr::get($this->config, 'path');

        if (empty($path) === true) {
            return;
        }

        $content = $this->files->get($path);

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

        $qualifiedName = $this->attributes['qualified_class'];
        $class = $this->attributes['class'];

        if ($qualifiedName && strpos($content, sprintf('use %s;', $qualifiedName)) === false) {
            $content = preg_replace_callback(
                '/namespace.+/',
                [$this, 'replaceServieProviderCallback'],
                $content
            );
        }

        if ($class && strpos($content, $this->singletonString($class)) === false) {
            $content = preg_replace_callback(
                '/protected function registerRepositories.+\n\s+{/',
                [$this, 'replaceServieProviderCallback'],
                $content
            );
        }

        $fixedContent = $this->useSortFixer->fix($content);

        $this->files->put(
            $path,
            $fixedContent ? $fixedContent : $content
        );
    }

    /**
     * replaceServieProviderCallback.
     *
     * @param array $match
     * @return string
     */
    private function replaceServieProviderCallback($match)
    {
        $qualifiedName = $this->attributes['qualified_class'];
        $class = $this->attributes['class'];
        $contractQualifiedName = $this->attributes['repository_contract_qualified_class'];

        if (Str::startsWith($match[0], 'namespace') === true) {
            return $match[0]."\n\n".
                sprintf("use %s as %sContract;\n", $contractQualifiedName, $class).
                sprintf("use %s;\n", $qualifiedName);
        }

        return $match[0]."\n".str_repeat(' ', 8).$this->singletonString($class);
    }

    private function singletonString($class)
    {
        return sprintf('$this->app->singleton(%sContract::class, %s::class);', $class, $class);
    }
}
