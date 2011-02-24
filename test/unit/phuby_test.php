<?php

class PhubyTest extends ztest\UnitTestCase {

    function test_should_create_call_function_for_class_when_autoloading() {
        Phuby::autoload('Object');
        ensure(function_exists('Object'));
    }

    function test_non_static_method_call_error_handler() {
        $context = array();
        assert_not_identical(false, Phuby::non_static_method_call_error_handler(2048, false, false, false, $context));
        assert_identical(false, Phuby::non_static_method_call_error_handler(false, false, false, false, $context));
    }

    function test_undefined_constant_error_handler() {
        $context = array();
        assert_not_identical(false, Phuby::undefined_constant_error_handler(8, 'constant Object', false, false, $context));
        assert_identical(false, Phuby::undefined_constant_error_handler(false, false, false, false, $context));
        assert_identical(false, Phuby::undefined_constant_error_handler(8, 'variable Object', false, false, $context));
    }

}