<?php

namespace EigenclassTest {
    class Basic extends \Object { }
    class User extends \Object {
        function name() { }
    }
    class Module { }
    class AnotherModule {
        function greet() { return true; }
    }
    class ExtendModule { }
}

namespace {
    class EigenclassTest extends ztest\UnitTestCase {

        function setup() {
            $this->user_class = Klass::instance('EigenclassTest\User');
            $this->user_class->extend('EigenclassTest\ExtendModule');
            $this->module = Klass::instance('EigenclassTest\Module');
            $this->another_module = Klass::instance('EigenclassTest\AnotherModule');
            $this->another_module->extend($this->another_module);
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

        function test_should_return_ancestors_for_class() {
            $ancestors = array('EigenclassTest\Basic', 'Eigenclass\InstanceMethods', 'Eigenclass', 'Klass\InstanceMethods', 'Klass', 'Object\InstanceMethods', 'Object');
            foreach ($ancestors as $index => $ancestor) $ancestors[$index] = Klass::instance($ancestor);
            assert_equal($ancestors, Klass::instance('EigenclassTest\Basic')->__class()->ancestors());
        }

        function test_should_return_ancestors_with_extended_modules() {
            $ancestors = array('EigenclassTest\ExtendModule', 'EigenclassTest\User', 'Eigenclass\InstanceMethods', 'Eigenclass', 'Klass\InstanceMethods', 'Klass', 'Object\InstanceMethods', 'Object');
            foreach ($ancestors as $index => $ancestor) $ancestors[$index] = Klass::instance($ancestor);
            assert_equal($ancestors, $this->user_class->__class()->ancestors());
        }

        function test_should_return_is_class() {
            ensure($this->user_class->__class()->is_class());
            ensure(!$this->user->__class()->is_class());
        }

        function test_module_should_be_able_to_extend_self() {
            ensure($this->another_module->greet());
        }

    }
}