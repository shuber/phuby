<?php

namespace ClassReflectorTest {
    class Mock { }

    class Dummy extends Mock {
        public $variable;
        static $class_variable;

        function method() { }
        static function class_method() { }
    }

    class User extends Dummy {
        public $name;
        static $table_name;

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
            $variables = $this->reflector->class_variables();
            $keys = array_keys($variables);
            sort($keys);
            assert_equal(array('class_variable', 'table_name'), $keys);
        }

        function test_should_return_class_variables_without_super() {
            $variables = $this->reflector->class_variables(false);
            $keys = array_keys($variables);
            sort($keys);
            assert_equal(array('table_name'), $keys);
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
            $variables = $this->reflector->instance_variables();
            $keys = array_keys($variables);
            sort($keys);
            assert_equal(array('name', 'variable'), $keys);
        }

        function test_should_return_instance_variables_without_super() {
            $variables = $this->reflector->instance_variables(false);
            $keys = array_keys($variables);
            sort($keys);
            assert_equal(array('name'), $keys);
        }

        function test_should_return_methods() {
            $methods = $this->reflector->methods();
            $keys = array_keys($methods);
            sort($keys);
            assert_equal(array('all', 'class_method', 'email', 'method'), $keys);
            foreach ($methods as $method) ensure(is_a($method, 'ReflectionMethod'));
        }

        function test_should_return_methods_without_super() {
            $methods = $this->reflector->methods(false);
            $keys = array_keys($methods);
            sort($keys);
            assert_equal(array('all', 'email'), $keys);
        }

        function test_should_set_name() {
            assert_equal($this->class, $this->reflector->name());
        }

        function test_should_return_reflection() {
            ensure(is_a($this->reflector->reflection(), 'ReflectionClass'));
        }

        function test_should_return_superclass() {
            assert_identical(ClassReflector::instance('ClassReflectorTest\Dummy'), $this->reflector->superclass());
        }

        function test_should_return_variables() {
            $variables = $this->reflector->variables();
            $keys = array_keys($variables);
            sort($keys);
            assert_equal(array('class_variable', 'name', 'table_name', 'variable'), $keys);
            foreach ($variables as $variable) ensure(is_a($variable, 'ReflectionProperty'));
        }

        function test_should_return_variables_without_super() {
            $variables = $this->reflector->variables(false);
            $keys = array_keys($variables);
            sort($keys);
            assert_equal(array('name', 'table_name'), $keys);
        }

        function test_should_store_and_return_instances() {
            $instance = ClassReflector::instance('ClassReflectorTest\Dummy');
            assert_identical($instance, ClassReflector::instance('ClassReflectorTest\Dummy'));
        }

    }
}