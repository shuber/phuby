<?php

namespace ObjectTest\User {
    class InstanceMethods {
        function greet() {
            return 'HI';
        }

        function respond_to_missing($method) {
            return $method == 'test_respond_to_missing';
        }

        function send_test($one, $two) {
            return $one.$two;
        }
    }

    class Module {
        function greet($name = '') {
            return $this->super().$name;
        }
    }
}

namespace ObjectTest {
    class User extends \Object {
        function name() {
            return 'Tom';
        }
    }

    class Child extends User {
        function name($last_name = '') {
            return $this->super().$last_name;
        }
    }

    class Instance extends \Object { }
    \Klass::instance('ObjectTest\User')->__include('ObjectTest\User\Module', 'ObjectTest\User\InstanceMethods');
}

namespace {
    class ObjectTest extends ztest\UnitTestCase {

        function setup() {
            $this->user = new ObjectTest\User;
            $this->child = new ObjectTest\Child;
        }

        function test_should_allocate() {
            ensure(is_a($this->user, 'ObjectTest\User'));
        }

        function test_should_pass_magic_get_calls_to_methods() {
            assert_equal('Tom', $this->user->name);
        }

        function test_should_call_mixed_in_method() {
            assert_equal('HI', $this->user->greet());
        }

        function test_should_call_raise_bad_method_call_exception() {
            $user = $this->user;
            assert_throws('BadMethodCallException', function() use ($user) { $user->invalid_method(); });
        }

        function test_should_return_eigenclass() {
            ensure(is_a($this->user->__class(), 'Eigenclass'));
            assert_identical(Klass::instance('ObjectTest\User'), $this->user->__class()->reference());
        }

        function test_should_call_method() {
            $methods = Klass::instance('ObjectTest\User\InstanceMethods')->reflection()->instance_methods();
            assert_equal('12', $this->user->call_method($methods['send_test'], array(1, 2)));
        }

        function test_should_return_callee() {
            $methods = Klass::instance('ObjectTest\User')->reflection()->instance_methods();
            assert_identical($methods['name'], $this->user->callee('name'));
        }

        function test_should_return_callee_from_included_module() {
            $methods = Klass::instance('ObjectTest\User\Module')->reflection()->instance_methods();
            assert_identical($methods['greet'], $this->user->callee('greet'));
        }

        function test_should_return_a_null_callee_when_method_is_undefined() {
            assert_equal(null, $this->user->callee('invalid_method'));
        }

        function test_method_defined() {
            ensure($this->user->method_defined('name'));
            ensure($this->user->method_defined('greet'));
            ensure(!$this->user->method_defined('invalid_method'));
        }

        function test_respond_to_should_check_respond_to_missing() {
            ensure($this->user->respond_to('test_respond_to_missing'));
        }

        function test_should_send() {
            assert_equal('12', $this->user->send('send_test', 1, 2));
        }

        function test_should_send_array() {
            assert_equal('12', $this->user->send_array('send_test', array(1, 2)));
        }

        function test_should_call_super() {
            assert_equal('Tom Jones', $this->child->name(' Jones'));
        }

        function test_should_call_super_from_included_module() {
            assert_equal('HI Doug', $this->user->greet(' Doug'));
        }

        function test_inspect() {
            assert_equal('#<'.$this->user->__class()->name().':'.$this->user->object_id().'>', $this->user->inspect());
        }

        function test_to_s_should_return_inspect() {
            ob_start();
            echo $this->user;
            $implicit = ob_get_clean();
            $concatenation = ''.$this->user.'';
            assert_all_equal($this->user->inspect(), $this->user->to_s(), $implicit, $concatenation);
        }

        function test_should_return_object_id() {
            $id = spl_object_hash($this->user);
            assert_equal($id, $this->user->object_id);
        }

        function test_should_intercept_calls_to_keyword_methods() {
            assert_all_equal($this->user->__class(), $this->user->class(), $this->user->send_array('class'), $this->user->send('class'));
        }

        function test_should_include_modules_in_instance() {
            $module = Klass::instance('ObjectTest\User\Module');
            $instance = new ObjectTest\Instance;
            $instance->__include($module);
            assert_in_array($module, $instance->__class()->included_modules());
            assert_not_in_array($module, Klass::instance('ObjectTest\Instance')->included_modules());
        }

    }
}