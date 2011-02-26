<?php

/**
 * Manages include paths, error handlers, and autoloading
 *
 * @package phuby
 * @author Sean Huber <shuber@huberry.com>
 * @abstract
**/
abstract class Environment {

    /**
     * The filename extension used by Environment::filename_for_class() when autoloading.
     * Defaults to '.php'
     *
     * @static
    **/
    static $autoload_filename_extension = '.php';

    /**
     * Stores registered custom error handlers.
     *
     * @static
    **/
    static $error_handlers = array();

    /**
     * Appends a path to the end of the include_path.
     * Returns the old include_path or false on failure.
     *
     * @param string $path
     * @return string
     * @static
    **/
    static function append_include_path($path) {
        return self::set_include_paths(array_merge(self::include_paths(), array($path)));
    }

    /**
     * Default autoload implementation.
     * Requires a file with the underscored version of a class name and subdirectories for each namespace.
     *
     * <code>
     * Environment::autoload('ActiveRecord\Base');  // => include_once 'active_record/base.php';
     * </code>
     *
     * @param string $class
     * @return void
     * @static
    **/
    static function autoload($class) {
        $filename = self::filename_for_class($class);
        // class_eval('NonExistentClass') throws warnings if we just "include_once $filename"
        foreach (static::include_paths() as $include_path) {
            $file = realpath($include_path.DIRECTORY_SEPARATOR.$filename);
            if (file_exists($file)) {
                include_once $file;
                break;
            }
        }
    }

    /**
     * Default error handler implementation.
     * Passes errors to the handlers registered with the Environment class.
     * If all custom handlers do not handle the error, then the error is passed to the default php handler.
     *
     * @param string $number
     * @param string $message
     * @param string $file
     * @param string $line
     * @param string $context
     * @return void|boolean
     * @static
    **/
    static function error_handler($number, $message, $file, $line, &$context) {
        foreach (self::$error_handlers as $handler) {
            if (call_user_func($handler, $number, $message, $file, $line, $context) !== false) return;
        }
        return false;
    }

    /**
     * Returns the underscored version of $class prefixed with its namespaces as directories
     *
     * <code>
     * Environment::filename_for_class('ActiveRecord\Base');  // => 'active_record/base.php'
     * </code>
     *
     * @param string $class
     * @return string
     * @static
    **/
    static function filename_for_class($class) {
        $namespaces = array_filter(preg_split('#\\\\|::#', $class));
        $parts = array_map(function($namespace) { return strtolower(preg_replace('/[^A-Z^a-z^0-9]+/', '_', preg_replace('/([a-z\d])([A-Z])/', '\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', $namespace)))); }, $namespaces);
        return implode(DIRECTORY_SEPARATOR, $parts).static::$autoload_filename_extension;
    }

    /**
     * Returns an array of the current include paths.
     *
     * @return array
     * @static
    **/
    static function include_paths() {
        return explode(PATH_SEPARATOR, get_include_path());
    }

    /**
     * Prepends a path to the beginning of the include_path.
     * Returns the old include_path or false on failure.
     *
     * @param string $path
     * @return string
     * @static
    **/
    static function prepend_include_path($path) {
        return self::set_include_paths(array_merge(array($path), self::include_paths()));
    }

    /**
     * Prepends a custom error handler with the Environment class.
     * If your custom error handler returns false, the error is passed to the next error handler registered with Environment.
     *
     * @param string|array $handler
     * @return void
     * @static
     * @link http://us.php.net/set_error_handler#function.set-error-handler.parameters
    **/
    static function register_error_handler($handler) {
        array_unshift(self::$error_handlers, $handler);
    }

    /**
     * Removes a path from the include_path.
     * Returns the old include_path or false on failure.
     *
     * @param string $path
     * @return string
     * @static
    **/
    static function remove_include_path($path) {
        $paths = array();
        foreach (self::include_paths() as $include_path) {
            if ($include_path != $path) array_push($paths, $include_path);
        }
        self::set_include_paths($paths);
    }

    /**
     * Replaces the current include_path with $paths.
     * Returns the old include_path or false on failure.
     *
     * @param array $paths
     * @return string
     * @static
    **/
    static function set_include_paths($paths) {
        if (is_array($paths)) $paths = implode(PATH_SEPARATOR, $paths);
        return set_include_path($paths);
    }

}