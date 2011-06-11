<?php

namespace Phuby\ObjectTest {
    class User extends \Phuby\Object {
        public $name;
        public $array = array(1, 2, 3);
        function public_method() { return true; }
        protected function protected_method() { return true; }
        private function private_method() { return true; }
    }

    class Admin extends User {
        public $password;
    }

    class Mixin extends \Phuby\Object {
        function public_method() { return true; }
    }
}

namespace Phuby {
    class ObjectTest extends \ztest\UnitTestCase {

        function setup() {
            $this->admin = new ObjectTest\Admin;
            $this->user = new ObjectTest\User;
        }

        function test___call() {
            ensure($this->user->__call('public_method'));
            ensure($this->user->__call('protected_method'));
            ensure($this->user->__call('private_method'));

            $user = $this->user;
            assert_throws('BadMethodCallException', function() use ($user) { $user->__call('invalid', array()); });
        }

        function test___call__() {
            $user_class =  __CLASS__.NS.'User';
            $protected_method = new \ReflectionMethod($user_class, 'protected_method');
            $protected_method->setAccessible(true);
            $private_method = new \ReflectionMethod($user_class, 'private_method');
            $private_method->setAccessible(true);
            $mixin_method = new \ReflectionMethod(__CLASS__.NS.'Mixin', 'public_method');

            ensure($this->admin->__call__($protected_method));
            ensure($this->admin->__call__($private_method));
            ensure($this->admin->__call__($mixin_method));
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

        function test___send__() {
            $user = $this->user;
            assert_equal($user->_class_(), $user->__send__('_class_'));
            assert_throws('BadMethodCallException', function() use ($user) { $user->__send__('invalid'); });
        }

        function test___send_array__() {
            $user = $this->user;
            assert_equal($user->_class_(), $user->__send_array__('_class_'));
            assert_throws('BadMethodCallException', function() use ($user) { $user->__send_array__('invalid'); });
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

        function test__class_() {
            ensure(is_a($this->user->_class_(), __NS__.'Eigenclass'));
        }

        function test_cast() {
            $this->user->name = 'Test';
            $admin_class = __CLASS__.NS.'Admin';
            $admin = $this->user->cast($admin_class);
            ensure(is_a($admin, $admin_class));
            assert_equal('Test', $admin->name);

            $admin->name = 'Steve';
            assert_equal('Steve', $this->user->name);

            $this->user->password = 'example';
            assert_equal('example', $admin->password);

            assert_equal($this->user->_class_(), $admin->_class_());

            $user_class = get_class($this->user);
            assert_identical($this->user, $this->user->cast($user_class));
            assert_equal($user_class, get_class($this->admin->cast($user_class)));
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

        function test_offsetExists() {
            $user = $this->user;
            assert_throws('BadMethodCallException', function() use ($user) { $user->offsetExists('test'); });
            assert_throws('BadMethodCallException', function() use ($user) { isset($user['test']); });
        }

        function test_offsetGet() {
            $user = $this->user;
            assert_throws('BadMethodCallException', function() use ($user) { $user->offsetGet('test'); });
            assert_throws('BadMethodCallException', function() use ($user) { $user['test']; });
        }

        function test_offsetSet() {
            $user = $this->user;
            assert_throws('BadMethodCallException', function() use ($user) { $user->offsetSet('test', true); });
            assert_throws('BadMethodCallException', function() use ($user) { $user['test'] = true; });
        }

        function test_offsetUnset() {
            $user = $this->user;
            assert_throws('BadMethodCallException', function() use ($user) { $user->offsetUnset('test'); });
            assert_throws('BadMethodCallException', function() use ($user) { unset($user['test']); });
        }

        function test_instance_variables() {
            ensure(array_key_exists('name', $this->user->instance_variables()));
            ensure(!array_key_exists('age', $this->user->instance_variables()));
            $this->user->instance_variable_set('age', 30);
            ensure(array_key_exists('age', $this->user->instance_variables()));
        }

        function test_protected_bind_instance_variables_to_properties() {
            $instance_variables = $this->user->instance_variables();
            assert_identical($this->user->array, $instance_variables['array']);
            assert_not_in_array('_instance_variables', array_keys($instance_variables));
        }

    }
}