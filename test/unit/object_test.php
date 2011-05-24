<?php

namespace ObjectTest\User {
    class InstanceMethods {
        function call() {
            return func_get_args();
        }

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
        static function test_static_method() {
            return true;
        }

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
            $this->instance_class = Klass::instance('ObjectTest\Instance');
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

        function test_is_a() {
            ensure($this->child->is_a('ObjectTest\Child'));
            ensure($this->child->is_a('ObjectTest\User'));
            ensure($this->child->is_a('ObjectTest\User\Module'));
            ensure($this->child->is_a('ObjectTest\Child', false));
            ensure(!$this->child->is_a('ObjectTest\User', false));
        }

        function test_should_include_modules_in_instance() {
            $module = Klass::instance('ObjectTest\User\Module');
            $instance = new ObjectTest\Instance;
            $instance->__include($module);
            assert_in_array($module, $instance->__class()->included_modules());
            assert_not_in_array($module, Klass::instance('ObjectTest\Instance')->included_modules());
        }

        function test_should_call_static_method_thru_eigenclass() {
            ensure($this->user->__class()->test_static_method());
        }

        function test_should_only_extend_in_eigenclass() {
            $module = Klass::instance('ObjectTest\User\Module');
            $instance = new ObjectTest\Instance;
            $instance->extend($module);
            assert_in_array($module, $instance->__class()->extended_modules());
            assert_not_in_array($module, $this->instance_class->extended_modules());
        }

        function test_should_invoke_by_calling_call() {
            $arguments = array(1, 2, 3);
            $user = $this->user;
            assert_equal(array($arguments), $user($arguments));
        }

        function test_should_read_and_write_property() {
            fail();
        }

        function test_should_read_and_write_included_property() {
            fail();
        }

        function test_should_get_instance_variable() {
            fail();
        }

        function test_should_set_instance_variable() {
            fail();
        }

        function test_instance_variable_defined() {
            fail();
        }

        function test_should_call_instance_variable_missing() {
            fail();
        }

        function test_instance_variable_isset() {
            fail();
        }

        function test_instance_variable_unset() {
            fail();
        }

    }
}