<?php

namespace KlassTest {
    class Base extends \Object { }
    class User extends Base { }
    class Module { }
    class AnotherModule { }
    class UserWithModules extends Base { }
}

namespace {
    class KlassTest extends ztest\UnitTestCase {

        function setup() {
            $this->user_class = new Klass('KlassTest\User');
            $this->user_class_with_modules = &Klass::instance('KlassTest\UserWithModules');
            $this->user_class_with_modules->__include('KlassTest\Module');
            $this->object_class = new Klass('Object');
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
            $ancestors = array(Klass::instance('KlassTest\AnotherModule'), Klass::instance('KlassTest\Module'), Klass::instance('KlassTest\UserWithModules'), Klass::instance('KlassTest\Base'), Klass::instance('Object\InstanceMethods'), Klass::instance('Object'));
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
            assert_equal(new Klass('KlassTest\Base'), $this->user_class->superclass());
            assert_equal(null, $this->object_class->superclass());
        }

        function test_should_return_same_instance() {
            assert_identical(Klass::instance('Object'), Klass::instance('Object'));
            assert_throws('InvalidArgumentException', function() { Klass::instance('KlassTest\NonExistentClass'); });
        }

    }
}