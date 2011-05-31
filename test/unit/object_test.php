<?php

namespace Phuby\ObjectTest {
    class User extends \Phuby\Object {
        public $name;
    }
}

namespace Phuby {
    class ObjectTest extends \ztest\UnitTestCase {

        function setup() {
            $this->user = new ObjectTest\User;
        }

        function test_getter() {
            $user = $this->user;

            $user->instance_variable_set('test', true);
            assert_equal(true, $user->test);

            assert_throws('\BadMethodCallException', function() use ($user) { $user->invalid; });
        }

        function test_setter() {
            $user = $this->user;

            assert_equal(null, $user->name);
            $user->name = 'Test';
            assert_equal('Test', $user->name);

            assert_throws('\BadMethodCallException', function() use ($user) { $user->invalid; });

            $user->instance_variable_set('invalid', true);
            assert_equal(true, $user->invalid);
        }

        function test_instance_variable_defined() {
            ensure(!$this->user->instance_variable_defined('invalid'));
            ensure($this->user->instance_variable_defined('name'));

            $this->user->instance_variable_set('test', true);
            ensure($this->user->instance_variable_defined('test'));
        }

        function test_instance_variable_get() {
            assert_all_equal(null, $this->user->name, $this->user->instance_variable_get('name'));
            $this->user->name = 'Test';
            assert_all_equal('Test', $this->user->name, $this->user->instance_variable_get('name'));
            assert_equal(null, $this->user->instance_variable_get('invalid'));
        }

        function test_instance_variable_isset() {
            ensure(!$this->user->instance_variable_isset('name'));
            $this->user->name = 'Test';
            ensure($this->user->instance_variable_isset('name'));

            ensure(!$this->user->instance_variable_isset('age'));
            $this->user->instance_variable_set('age', 30);
            ensure($this->user->instance_variable_isset('age'));
        }

        function test_instance_variable_set() {
            assert_equal(null, $this->user->name);
            $this->user->instance_variable_set('name', 'Test');
            assert_equal('Test', $this->user->name);
        }

    }
}