<?php

class EnvironmentTest extends ztest\UnitTestCase {

    function setup() {
        $this->include_path = get_include_path();
        $this->autoload_filename_extension = Environment::$autoload_filename_extension;
        $this->error_handlers = Environment::$error_handlers;
    }

    function teardown() {
        set_include_path($this->include_path);
        Environment::$autoload_filename_extension = $this->autoload_filename_extension;
        Environment::$error_handlers = $this->error_handlers;
    }

    function exception_error_handler($number, $message, $file, $line, &$context) {
        throw new RuntimeException;
    }

    function passing_error_handler($number, $message, $file, $line, &$context) {
        $this->passing_error_handler_called = true;
    }

    function failing_error_handler($number, $message, $file, $line, &$context) {
        $this->failing_error_handler_called = true;
        return false;
    }

    function test_should_append_include_path() {
        Environment::append_include_path(__DIR__);
        assert_equal($this->include_path.PATH_SEPARATOR.__DIR__, get_include_path());
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
        assert_equal('user.php', Environment::filename_for_class('User'));
        assert_equal('namespaced/user.php', Environment::filename_for_class('Namespaced\User'));
        assert_equal('namespaced/user.php', Environment::filename_for_class('\Namespaced\User'));
        assert_equal('namespaced/user.php', Environment::filename_for_class('Namespaced::User'));
        assert_equal('namespaced/user.php', Environment::filename_for_class('::Namespaced::User'));
        assert_equal('camel_cased/user.php', Environment::filename_for_class('CamelCased\User'));
        assert_equal('under_scored/user.php', Environment::filename_for_class('under_scored\User'));
        assert_equal('capital_under_scored/user.php', Environment::filename_for_class('Capital_under_scored\User'));
        assert_equal('user.php', Environment::filename_for_class('user'));
        assert_equal('user.php', Environment::filename_for_class('USER'));
        assert_equal('user99.php', Environment::filename_for_class('User99'));
    }

    function test_should_return_correct_filename_for_class_with_different_extension() {
        Environment::$autoload_filename_extension = '.inc';
        assert_equal('user.inc', Environment::filename_for_class('User'));
    }

    function test_should_return_include_paths() {
        assert_equal(explode(PATH_SEPARATOR, $this->include_path), Environment::include_paths());
    }

    function test_should_prepend_include_path() {
        Environment::prepend_include_path(__DIR__);
        assert_equal(__DIR__.PATH_SEPARATOR.$this->include_path, get_include_path());
    }

    function test_should_register_error_handler() {
        Environment::register_error_handler('test');
        assert_equal(array_merge(array('test'), $this->error_handlers), Environment::$error_handlers);
    }

    function test_should_remove_include_path() {
        $paths = explode(PATH_SEPARATOR, $this->include_path);
        Environment::remove_include_path(array_pop($paths));
        assert_equal(implode(PATH_SEPARATOR, $paths), get_include_path());
    }

    function test_should_set_include_path() {
        $paths = array('test', 'paths');
        Environment::set_include_paths($paths);
        assert_equal(implode(PATH_SEPARATOR, $paths), get_include_path());
    }

    function test_should_set_include_path_as_string() {
        $paths = 'test'.PATH_SEPARATOR.'paths';
        Environment::set_include_paths($paths);
        assert_equal($paths, get_include_path());
    }

}