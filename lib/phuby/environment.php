<?php

namespace Phuby {
    /**
     * Manages include paths, error handlers, and autoloading
     *
     * @package phuby
     * @author Sean Huber <shuber@huberry.com>
     * @abstract
    **/
    abstract class Environment {

        const SPL_AUTOLOAD_EXTENSION_SEPARATOR = ',';

        /**
         * Stores registered custom error handlers.
         *
         * @static
        **/
        static $error_handlers = array();

        /**
         * Appends an extension to the end of spl_autoload_extensions.
         * Returns the new spl_autoload_extensions string.
         *
         * @param string $extension
         * @return string
         * @static
        **/
        static function append_autoload_extension($extension) {
            return self::set_autoload_extensions(array_merge(self::autoload_extensions(), array($extension)));
        }

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
            foreach (self::autoload_extensions() as $extension) {
                if ($file = self::resolve_include_path($filename.$extension)) {
                    include $file;
                    break;
                }
            }
        }

        /**
         * Returns an array of autoload extensions defined with spl_autoload_extensions.
         *
         * @return array
         * @static
        **/
        static function autoload_extensions() {
            return preg_split('#\s*'.self::SPL_AUTOLOAD_EXTENSION_SEPARATOR.'\s*#', spl_autoload_extensions());
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
            return implode(DS, $parts);
        }

        /**
         * Returns an array of the current include paths.
         *
         * @return array
         * @static
        **/
        static function include_paths() {
            return explode(PS, get_include_path());
        }

        /**
         * Prepends an extension to the beginning of spl_autoload_extensions.
         * Returns the new spl_autoload_extensions string.
         *
         * @param string $extension
         * @return string
         * @static
        **/
        static function prepend_autoload_extension($extension) {
            return self::set_autoload_extensions(array_merge(array($extension), self::autoload_extensions()));
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
         * Removes an extension from the spl_autoload_extensions.
         * Returns the new spl_autoload_extensions string.
         *
         * @param string $extension
         * @return string
         * @static
        **/
        static function remove_autoload_extension($extension) {
            return self::set_autoload_extensions(array_diff(self::autoload_extensions(), array($extension)));
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
            return self::set_include_paths(array_diff(self::include_paths(), array($path)));
        }

        /**
         * Returns the fully resolved include path of $filename, or false if it doesn't exist in include_paths.
         *
         * @param string $filename
         * @return string | false
         * @static
        **/
        static function resolve_include_path($filename) {
            $resolved_include_path = false;
            if (function_exists('stream_resolve_include_path')) {
                $resolved_include_path = stream_resolve_include_path($filename);
            } else {
                foreach (self::include_paths() as $include_path) {
                    $file = realpath($include_path.DS.$filename);
                    if (file_exists($file)) {
                        $resolved_include_path = $file;
                        break;
                    }
                }
            }
            return $resolved_include_path;
        }

        /**
         * Sets spl_autload_extensions to the string or array of extensions specified.
         *
         * @param string | array $extensions
         * @return string
         * @static
        **/
        static function set_autoload_extensions($extensions) {
            if (is_array($extensions)) $extensions = implode(self::SPL_AUTOLOAD_EXTENSION_SEPARATOR, $extensions);
            return spl_autoload_extensions($extensions);
        }

        /**
         * Replaces the current include_path with $paths.
         * Returns the old include_path or false on failure.
         *
         * @param string | array $paths
         * @return string
         * @static
        **/
        static function set_include_paths($paths) {
            if (is_array($paths)) $paths = implode(PS, $paths);
            return set_include_path($paths);
        }

    }
}