<?php

namespace EigenclassTest {
    class User extends \Object {
        function name() { }
    }
    class Module { }
    class AnotherModule {
        function greet() { }
    }
}

namespace {
    class EigenclassTest extends ztest\UnitTestCase {

        function setup() {
            $this->user_class = Klass::instance('EigenclassTest\User');
            $this->module = Klass::instance('EigenclassTest\Module');
            $this->another_module = Klass::instance('EigenclassTest\AnotherModule');
            $this->user = new EigenclassTest\User;
        }

        function test_should_include_into_instance() {
            assert_not_in_array($this->module, $this->user_class->ancestors());
            assert_not_in_array($this->module, $this->user->__class()->ancestors());
            $this->user->__class()->__include($this->module, true);
            assert_in_array($this->module, $this->user->__class()->ancestors());
            assert_not_in_array($this->module, $this->user_class->ancestors());
        }

        function test_should_return_callee() {
            $methods = Klass::instance('EigenclassTest\User')->reflection()->instance_methods();
            assert_identical($methods['name'], $this->user->__class()->callee('name'));
        }

        function test_should_return_callee_from_included_module() {
            $methods = Klass::instance('EigenclassTest\AnotherModule')->reflection()->instance_methods();
            $this->user->__include($this->another_module);
            assert_identical($methods['greet'], $this->user->__class()->callee('greet'));
        }

        function test_should_return_a_null_callee_when_method_is_undefined() {
            assert_equal(null, $this->user->__class()->callee('invalid_method'));
        }

        function test_should_return_class_reference() {
            assert_identical($this->user_class, $this->user->__class()->reference());
        }

        function test_should_return_object() {
            assert_identical($this->user_class, $this->user_class->__class()->object());
        }

    }
}