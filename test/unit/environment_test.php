<?php

namespace Phuby {
    class EnvironmentTest extends \ztest\UnitTestCase {

        function setup() {
            $this->include_path = get_include_path();
            $this->autoload_extensions = spl_autoload_extensions();
            $this->error_handlers = Environment::$error_handlers;
        }

        function teardown() {
            set_include_path($this->include_path);
            spl_autoload_extensions($this->autoload_extensions);
            Environment::$error_handlers = $this->error_handlers;
        }

        function exception_error_handler($number, $message, $file, $line, &$context) {
            throw new \RuntimeException;
        }

        function passing_error_handler($number, $message, $file, $line, &$context) {
            $this->passing_error_handler_called = true;
        }

        function failing_error_handler($number, $message, $file, $line, &$context) {
            $this->failing_error_handler_called = true;
            return false;
        }

        function test_should_call_error_handlers() {
            Environment::register_error_handler(array($this, 'exception_error_handler'));
            assert_throws('RuntimeException', function() {
                $context = array();
                Environment::error_handler(false, false, false, false, $context);
            });

            Environment::register_error_handler(array($this, 'passing_error_handler'));
            Environment::error_handler(false, false, false, false, $context);
            ensure($this->passing_error_handler_called);

            $this->passing_error_handler_called = false;
            Environment::register_error_handler(array($this, 'failing_error_handler'));
            Environment::error_handler(false, false, false, false, $context);
            ensure($this->failing_error_handler_called);
            ensure($this->passing_error_handler_called);
        }

        function test_should_return_correct_filename_for_class() {
            assert_equal('user',                      Environment::filename_for_class('User'));
            assert_equal('namespaced/user',           Environment::filename_for_class('Namespaced\User'));
            assert_equal('namespaced/user',           Environment::filename_for_class('\Namespaced\User'));
            assert_equal('namespaced/user',           Environment::filename_for_class('Namespaced::User'));
            assert_equal('namespaced/user',           Environment::filename_for_class('::Namespaced::User'));
            assert_equal('camel_cased/user',          Environment::filename_for_class('CamelCased\User'));
            assert_equal('under_scored/user',         Environment::filename_for_class('under_scored\User'));
            assert_equal('capital_under_scored/user', Environment::filename_for_class('Capital_under_scored\User'));
            assert_equal('user',                      Environment::filename_for_class('user'));
            assert_equal('user',                      Environment::filename_for_class('USER'));
            assert_equal('user99',                    Environment::filename_for_class('User99'));
        }

        function test_should_return_include_paths() {
            assert_equal(explode(PS, $this->include_path), Environment::include_paths());
        }

        function test_should_append_include_path() {
            Environment::append_include_path(__DIR__);
            assert_equal($this->include_path.PS.__DIR__, get_include_path());
        }

        function test_should_prepend_include_path() {
            Environment::prepend_include_path(__DIR__);
            assert_equal(__DIR__.PS.$this->include_path, get_include_path());
        }

        function test_should_register_error_handler() {
            Environment::register_error_handler('test');
            assert_equal(array_merge(array('test'), $this->error_handlers), Environment::$error_handlers);
        }

        function test_should_remove_include_path() {
            $paths = explode(PS, $this->include_path);
            Environment::remove_include_path(array_pop($paths));
            assert_equal(implode(PS, $paths), get_include_path());
        }

        function test_should_set_include_path() {
            $paths = array('test', 'paths');
            Environment::set_include_paths($paths);
            assert_equal(implode(PS, $paths), get_include_path());
        }

        function test_should_set_include_path_as_string() {
            $paths = 'test'.PS.'paths';
            Environment::set_include_paths($paths);
            assert_equal($paths, get_include_path());
        }

        function test_should_return_autoload_extensions() {
            assert_equal(explode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, $this->autoload_extensions), Environment::autoload_extensions());
        }

        function test_should_append_autoload_extension() {
            Environment::append_autoload_extension('.test');
            assert_equal($this->autoload_extensions.Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR.'.test', spl_autoload_extensions());
        }

        function test_should_prepend_autoload_extension() {
            Environment::prepend_autoload_extension('.test');
            assert_equal('.test'.Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR.$this->autoload_extensions, spl_autoload_extensions());
        }

        function test_should_remove_autoload_extension() {
            $extensions = explode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, $this->autoload_extensions);
            Environment::remove_autoload_extension(array_pop($extensions));
            assert_equal(implode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, $extensions), spl_autoload_extensions());
        }

        function test_should_set_autoload_extensions() {
            $extensions = array('.test', '.example');
            Environment::set_autoload_extensions($extensions);
            assert_equal(implode(Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR, $extensions), spl_autoload_extensions());
        }

        function test_should_set_autoload_extensions_as_string() {
            $extensions = '.test'.Environment::SPL_AUTOLOAD_EXTENSION_SEPARATOR.'.example';
            Environment::set_autoload_extensions($extensions);
            assert_equal($extensions, spl_autoload_extensions());
        }

    }
}