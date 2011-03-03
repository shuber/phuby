<?php

namespace KlassTest {
    class Base extends \Object { }
    class User extends Base { }
    class Module {
        function initialize($string) { echo $string; }
        static function static_method() { }
        function test_method() { }
        function another_test_method() { }
    }
    class AnotherModule { }
    class ExtendModule {
        function test_static_module_method() { return true; }
    }
    class AnotherExtendModule {
        function test_another_static_module_method() { return true; }
    }
    class UserWithModules extends Base {
        static function test_static_method() { return true; }
    }
    class Callbacks extends \Object { }
    class IncludedTestModule {
        static function included($base) {
            echo 'included by '.$base->object_id();
        }
    }
    class ExtendedTestModule {
        static function extended($base) {
            echo 'extended by '.$base->object_id();
        }
    }
}

namespace {
    class KlassTest extends ztest\UnitTestCase {

        function setup() {
            $this->user_class = new Klass('KlassTest\User');
            $this->user_class_with_modules = Klass::instance('KlassTest\UserWithModules');
            $this->user_class_with_modules->__include('KlassTest\Module');
            $this->user_class_with_modules->extend('KlassTest\ExtendModule');
            $this->object_class = new Klass('Object');
            $this->callbacks_class = Klass::instance('KlassTest\Callbacks');
            Klass::instance('KlassTest\Module')->__include('KlassTest\AnotherModule');
        }

        function test_should_create_a_new_real_class_at_runtime() {
            ensure(!class_exists('KlassTest\RuntimeCreatedClass'));
            $class = new Klass('KlassTest\RuntimeCreatedClass');
            ensure(class_exists('KlassTest\RuntimeCreatedClass'));
        }

        function test_should_not_create_a_new_real_class_at_runtime() {
            ensure(!class_exists('KlassTest\RuntimeCreatedClass2'));
            assert_throws('InvalidArgumentException', function() { new Klass('KlassTest\RuntimeCreatedClass2', null, false); });
            ensure(!class_exists('KlassTest\RuntimeCreatedClass2'));
        }

        function test_should_add_module_to_included_modules() {
            $property = new \ReflectionProperty($this->user_class_with_modules, '_included_modules');
            $property->setAccessible(true);
            assert_equal(array(Klass::instance('KlassTest\Module')), $property->getValue($this->user_class_with_modules));
        }

        function test_should_throw_cyclic_include_error() {
            assert_throws('InvalidArgumentException', function() {
                Klass::instance('KlassTest\AnotherModule')->__include('KlassTest\Module');
            });
        }

        function test_should_return_ancestors() {
            $ancestors = array(Klass::instance('KlassTest\User'), Klass::instance('KlassTest\Base'), Klass::instance('Object\InstanceMethods'), Klass::instance('Object'));
            assert_equal($ancestors, $this->user_class->ancestors());
        }

        function test_should_return_ancestors_with_unique_included_modules() {
            $ancestors = array('KlassTest\AnotherModule', 'KlassTest\Module', 'KlassTest\UserWithModules', 'KlassTest\Base', 'Object\InstanceMethods', 'Object');
            foreach ($ancestors as $index => $ancestor) $ancestors[$index] = Klass::instance($ancestor);
            assert_equal($ancestors, $this->user_class_with_modules->ancestors());
            Klass::instance('KlassTest\UserWithModules')->__include('KlassTest\AnotherModule');
            assert_equal($ancestors, $this->user_class_with_modules->ancestors());
        }

        function test_should_return_unique_included_modules() {
            $included_modules = array(Klass::instance('KlassTest\AnotherModule'), Klass::instance('KlassTest\Module'), Klass::instance('Object\InstanceMethods'));
            assert_equal($included_modules, $this->user_class_with_modules->included_modules());
            Klass::instance('KlassTest\UserWithModules')->__include('KlassTest\AnotherModule');
            assert_equal($included_modules, $this->user_class_with_modules->included_modules());
        }

        function test_should_return_correct_name() {
            assert_equal('KlassTest\User', $this->user_class->name());
        }

        function test_should_return_class_reflector() {
            assert_identical(ClassReflector::instance('KlassTest\User'), $this->user_class->reflection());
        }

        function test_should_return_superclass() {
            assert_equal(Klass::instance('KlassTest\Base'), $this->user_class->superclass());
            assert_equal(null, $this->object_class->superclass());
        }

        function test_should_return_same_instance() {
            $object = Klass::instance('Object');
            assert_identical($object, Klass::instance('Object'));
            assert_identical($object, Klass::instance($object));
            assert_throws('InvalidArgumentException', function() { Klass::instance('KlassTest\NonExistentClass'); });
        }

        function test_should_return_to_s() {
            ob_start();
            echo $this->user_class;
            $implicit = ob_get_clean();
            $concatenation = ''.$this->user_class.'';
            assert_all_equal($this->user_class->name(), $this->user_class->to_s(), $implicit, $concatenation);
        }

        function test_should_return_instance_methods() {
            $methods = $this->user_class_with_modules->instance_methods();
            assert_in_array('test_method', $methods);
            assert_not_in_array('static_test_method', $methods);
        }

        function test_should_return_instance_methods_without_super() {
            $methods = $this->user_class_with_modules->instance_methods();
            $methods_without_super = $this->user_class_with_modules->instance_methods(false);
            assert_not_equal($methods, $methods_without_super);
            ensure(count($methods) > count($methods_without_super));
            sort($methods_without_super);
            assert_equal(array('another_test_method', 'initialize', 'test_method'), $methods_without_super);
        }

        function test_should_call_class_method() {
            ensure(KlassTest\UserWithModules::test_static_method());
            ensure($this->user_class_with_modules->test_static_method());
        }

        function test_should_call_extended_module_method() {
            ensure(KlassTest\UserWithModules::test_static_module_method());
            ensure($this->user_class_with_modules->test_static_module_method());
        }

        function test_should_extend_module() {
            $module = Klass::instance('KlassTest\ExtendModule');
            assert_in_array($module, $this->user_class_with_modules->__class()->included_modules());
        }

        function test_should_return_extended_modules() {
            assert_equal(array(), $this->user_class->extended_modules());
            assert_equal(array(Klass::instance('KlassTest\ExtendModule')), $this->user_class_with_modules->extended_modules());
        }

        function test_should_allocate_object() {
            ensure(is_a($this->user_class->allocate(), $this->user_class->name()));
        }

        function test_should_initialize_object() {
            ob_start();
            $instance = $this->user_class_with_modules->new('test');
            ensure(is_a($instance, $this->user_class_with_modules->name()));
            assert_equal('test', ob_get_clean());
        }

        function test_should_call_included() {
            ob_start();
            $this->callbacks_class->__include('KlassTest\IncludedTestModule');
            assert_equal('included by '.$this->callbacks_class->object_id(), ob_get_clean());
        }

        function test_should_call_extended() {
            ob_start();
            $this->callbacks_class->extend('KlassTest\ExtendedTestModule');
            assert_equal('extended by '.$this->callbacks_class->object_id(), ob_get_clean());
        }

    }
}