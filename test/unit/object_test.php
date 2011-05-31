<?php

namespace Phuby\ObjectTest {
    class User extends \Phuby\Object {
        public $name;
    }

    class Admin extends \Phuby\Object {
        public $password;
    }
}

namespace Phuby {
    class ObjectTest extends \ztest\UnitTestCase {

        function setup() {
            $this->user = new ObjectTest\User;
        }

        function test___get() {
            $user = $this->user;

            $user->instance_variable_set('test', true);
            assert_equal(true, $user->test);

            assert_throws('\BadMethodCallException', function() use ($user) { $user->invalid; });
        }

        function test___id__() {
            assert_equal(spl_object_hash($this->user), $this->user->__id__());
        }

        function test___isset() {
            ensure(!isset($this->user->name));
            $this->user->name = 'Test';
            ensure(isset($this->user->name));

            ensure(!isset($this->user->age));
            $this->user->instance_variable_set('age', 30);
            ensure(isset($this->user->age));
        }

        function test___set() {
            $user = $this->user;

            assert_equal(null, $user->name);
            $user->name = 'Test';
            assert_equal('Test', $user->name);

            assert_throws('\BadMethodCallException', function() use ($user) { $user->invalid; });

            $user->instance_variable_set('invalid', true);
            assert_equal(true, $user->invalid);
        }

        function test___toString() {
            $user = $this->user;
            assert_throws('\BadMethodCallException', function() use ($user) { $user->__toString(); });
        }

        function test___unset() {
            $this->user->name = 'Test';
            ensure(isset($this->user->name));
            unset($this->user->name);
            ensure(!isset($this->user->name));

            $this->user->instance_variable_set('age', 30);
            ensure(isset($this->user->age));
            unset($this->user->age);
            ensure(!isset($this->user->age));
        }

        function test_cast() {
            $this->user->name = 'Test';
            $admin = $this->user->cast(__CLASS__.NS.'Admin');
            ensure(is_a($admin, __CLASS__.NS.'Admin'));
            assert_equal('Test', $admin->name);

            $admin->name = 'Steve';
            assert_equal('Steve', $this->user->name);

            $this->user->password = 'example';
            assert_equal('example', $admin->password);
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

            $this->user->instance_variable_set('invalid', 'Test');
            assert_equal('Test', $this->user->invalid);
        }

        function test_instance_variable_unset() {
            $this->user->name = 'Test';
            ensure(isset($this->user->name));
            $this->user->instance_variable_unset('name');
            ensure(!isset($this->user->name));

            $this->user->instance_variable_set('age', 30);
            ensure(isset($this->user->age));
            $this->user->instance_variable_unset('age');
            ensure(!isset($this->user->age));
        }

        function test_instance_variables() {
            ensure(array_key_exists('name', $this->user->instance_variables()));
            ensure(!array_key_exists('age', $this->user->instance_variables()));
            $this->user->instance_variable_set('age', 30);
            ensure(array_key_exists('age', $this->user->instance_variables()));
        }

    }
}