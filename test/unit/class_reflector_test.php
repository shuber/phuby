<?php

namespace ClassReflectorTest {
    class Mock { }

    class Dummy extends Mock {
        public $variable = 'test';
        static $class_variable = 'test';

        function method() { }
        static function class_method() { }
    }

    class User extends Dummy {
        public $name;
        static $table_name = 'users';

        function email() { }
        static function all() { }
    }
}

namespace {
    class ClassReflectorTest extends ztest\UnitTestCase {

        function setup() {
            $this->class = 'ClassReflectorTest\User';
            $this->reflector = new ClassReflector($this->class);
        }

        function test_should_use_get_magic_method() {
            assert_equal($this->reflector->name(), $this->reflector->name);
        }

        function test_should_return_ancestors() {
            $ancestors = array(
                'ClassReflectorTest\Dummy' => new ClassReflector('ClassReflectorTest\Dummy'),
                'ClassReflectorTest\Mock'  => new ClassReflector('ClassReflectorTest\Mock')
            );
            assert_equal($ancestors, $this->reflector->ancestors());
        }

        function test_should_return_class_methods() {
            $methods = $this->reflector->class_methods();
            $keys = array_keys($methods);
            sort($keys);
            assert_equal(array('all', 'class_method'), $keys);
        }

        function test_should_return_class_methods_without_super() {
            $methods = $this->reflector->class_methods(false);
            $keys = array_keys($methods);
            sort($keys);
            assert_equal(array('all'), $keys);
        }

        function test_should_return_class_variables() {
            //
        }

        function test_should_return_class_variables_without_super() {
            //
        }

        function test_should_return_instance_methods() {
            $methods = $this->reflector->instance_methods();
            $keys = array_keys($methods);
            sort($keys);
            assert_equal(array('email', 'method'), $keys);
        }

        function test_should_return_instance_methods_without_super() {
            $methods = $this->reflector->instance_methods(false);
            $keys = array_keys($methods);
            sort($keys);
            assert_equal(array('email'), $keys);
        }

        function test_should_return_instance_variables() {
            // 
        }

        function test_should_return_instance_variables_without_super() {
            // 
        }

        function test_should_return_methods() {
            // 
        }

        function test_should_return_methods_without_super() {
            // 
        }

        function test_should_set_name() {
            assert_equal($this->class, $this->reflector->name());
        }

        function test_should_return_reflection() {
            ensure(is_a($this->reflector->reflection(), 'ReflectionClass'));
        }

        function test_should_return_superclass() {
            // 
        }

        function test_should_return_variables() {
            // 
        }

        function test_should_store_and_return_instances() {
            $instance = ClassReflector::instance('ClassReflectorTest\Dummy');
            assert_identical($instance, ClassReflector::instance('ClassReflectorTest\Dummy'));
        }

    }
}