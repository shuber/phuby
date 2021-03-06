<?php

namespace Phuby;

class Environment {
    static function append_include_path($path) {
        $include_path = get_include_path();
        $include_path .= PATH_SEPARATOR.$path;

        return set_include_path($include_path);
    }

    static function autoload($class) {
        $filename = self::filename($class);

        if ($file = stream_resolve_include_path($filename))
            include $file;
    }

    static function filename($class) {
        $namespaces = array_filter(preg_split('#\\\\|::#', $class));
        $formatter = function($namespace) {
            $part = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', $namespace);
            $part = preg_replace('/([a-z\d])([A-Z])/', '\1_\2', $part);
            $part = preg_replace('/[^A-Z^a-z^0-9]+/', '_', $part);
            return strtolower($part);
        };

        $parts = array_map($formatter, $namespaces);

        return implode(DIRECTORY_SEPARATOR, $parts).'.php';
    }

    static function initialize($root) {
        self::append_include_path($root);
        spl_autoload_register(__CLASS__.'::autoload');
    }
}