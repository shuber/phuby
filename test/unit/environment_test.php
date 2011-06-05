<?php

namespace Phuby\EnvironmentTest {
    abstract class ErrorHandler {

        static $failing_error_handler_called = false;
        static $passing_error_handler_called = false;

        static function exception_error_handler($number, $message, $file, $line, &$context) {
            throw new \RuntimeException;
        }

        static function failing_error_handler($number, $message, $file, $line, &$context) {
            self::$failing_error_handler_called = true;
            return false;
        }

        static function passing_error_handler($number, $message, $file, $line, &$context) {
            self::$passing_error_handler_called = true;
        }

    }
}

namespace Phuby {
    class EnvironmentTest extends \ztest\UnitTestCase {

        function setup() {
            $this->autoload_extensions = spl_autoload_extensions();
            $this->error_handlers = Environment::error_handlers();
            $this->include_path = get_include_path();
        }

        function teardown() {
            spl_autoload_extensions($this->autoload_extensions);
            Environment::set_error_handlers($this->error_handlers);
            set_include_path($this->include_path);
        }

        function test_append_autoload_extension() {
            $old_autoload_extensions = Environment::append_autoload_extension('.test');
            assert_equal($this->autoload_extensions.Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR.'.test', spl_autoload_extensions());
            assert_equal($this->autoload_extensions, $old_autoload_extensions);
        }

        function test_append_error_handler() {
            $old_error_handlers = Environment::append_error_handler('test');
            assert_equal(array_merge($this->error_handlers, array('test')), Environment::error_handlers());
            assert_equal($this->error_handlers, $old_error_handlers);
        }

        function test_append_include_path() {
            $old_include_path = Environment::append_include_path(__DIR__);
            assert_equal($this->include_path.PS.__DIR__, get_include_path());
            assert_equal($this->include_path, $old_include_path);
        }

        function test_autoload_extensions() {
            assert_equal(explode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, spl_autoload_extensions()), Environment::autoload_extensions());
        }

        function test_error_handler() {
            $class = __CLASS__.NS.'ErrorHandler';
            $error_handler = function() {
                $context = array();
                return Environment::error_handler(0, 'message', 'file', 'line', $context);
            };
            $handlers = function($method) use ($class) { return array(array($class, $method)); };

            Environment::set_error_handlers($handlers('exception_error_handler'));
            assert_throws('RuntimeException', $error_handler);

            Environment::set_error_handlers($handlers('failing_error_handler'));
            $error_handler();
            ensure($class::$failing_error_handler_called);

            Environment::set_error_handlers($handlers('passing_error_handler'));
            $error_handler();
            ensure($class::$passing_error_handler_called);
        }

        function test_error_handlers() {
            assert_array($this->error_handlers);
            ensure(!empty($this->error_handlers));
        }

        function test_filename_for_class() {
            $assertions = array(
                'User'                            => 'user',
                'user'                            => 'user',
                'USER'                            => 'user',
                'User99'                          => 'user99',
                'Namespaced\User'                 => 'namespaced/user',
                'Namespaced::User'                => 'namespaced/user',
                '\Namespaced\User'                => 'namespaced/user',
                '\Namespaced::User'               => 'namespaced/user',
                '::Namespaced::User'              => 'namespaced/user',
                '::Namespaced\User'               => 'namespaced/user',
                'CamelCased\User'                 => 'camel_cased/user',
                'under_scored\User'               => 'under_scored/user',
                'CamelCase_and_Under_Scored\User' => 'camel_case_and_under_scored/user'
            );
            foreach ($assertions as $class => $filename) {
                assert_equal($filename, Environment::filename_for_class($class));
            }
        }

        function test_include_paths() {
            assert_equal(explode(PS, get_include_path()), Environment::include_paths());
        }

        function test_prepend_autoload_extension() {
            $old_autoload_extensions = Environment::prepend_autoload_extension('.test');
            assert_equal('.test'.Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR.$this->autoload_extensions, spl_autoload_extensions());
            assert_equal($this->autoload_extensions, $old_autoload_extensions);
        }

        function test_prepend_error_handler() {
            $old_error_handlers = Environment::prepend_error_handler('test');
            assert_equal(array_merge(array('test'), $this->error_handlers), Environment::error_handlers());
            assert_equal($this->error_handlers, $old_error_handlers);
        }

        function test_prepend_include_path() {
            $old_include_path = Environment::prepend_include_path(__DIR__);
            assert_equal(__DIR__.PS.$this->include_path, get_include_path());
            assert_equal($this->include_path, $old_include_path);
        }

        function test_remove_autoload_extension() {
            $extensions = explode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, spl_autoload_extensions());
            $old_extensions = Environment::remove_autoload_extension(array_pop($extensions));
            assert_equal(implode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, $extensions), spl_autoload_extensions());
            assert_equal($this->autoload_extensions, $old_extensions);
        }

        function test_remove_error_handler() {
            $handlers = Environment::error_handlers();
            $old_handlers = Environment::remove_error_handler(array_pop($handlers));
            assert_equal($handlers, Environment::error_handlers());
            assert_equal($this->error_handlers, $old_handlers);
        }

        function test_remove_include_path() {
            $paths = explode(PS, get_include_path());
            $old_paths = Environment::remove_include_path(array_pop($paths));
            assert_equal(implode(PS, $paths), get_include_path());
            assert_equal($this->include_path, $old_paths);
        }

        function test_resolve_include_path() {
            $filename = 'object_test.php';
            ensure(!Environment::resolve_include_path($filename));

            Environment::append_include_path(__DIR__);
            assert_equal(__DIR__.DS.$filename, Environment::resolve_include_path($filename));
        }

        function test_set_autoload_extensions() {
            $extensions = array('.test', '.extension');
            $old_autoload_extensions = Environment::set_autoload_extensions($extensions);
            assert_equal(implode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, $extensions), spl_autoload_extensions());
            assert_equal($this->autoload_extensions, $old_autoload_extensions);

            $extensions = '.test'.Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR.'.string'.Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR.'.extension';
            Environment::set_autoload_extensions($extensions);
            assert_equal($extensions, spl_autoload_extensions());
        }

        function test_set_error_handlers() {
            $handlers = array('test');
            $old_handlers = Environment::set_error_handlers($handlers);
            assert_equal($handlers, Environment::error_handlers());
            assert_equal($this->error_handlers, $old_handlers);
        }

        function test_set_include_paths() {
            $paths = array('test', 'paths');
            $old_include_path = Environment::set_include_paths($paths);
            assert_equal(implode(PS, $paths), get_include_path());
            assert_equal($this->include_path, $old_include_path);

            $paths = 'test'.PS.'string'.PS.'paths';
            Environment::set_include_paths($paths);
            assert_equal($paths, get_include_path());
        }

    }
}