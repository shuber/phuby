<?php

namespace Phuby\Core\ReflectionClassTest {
    class User extends \Phuby\Object {
        function instance_method() { }
        static function static_method() { }
    }

    class Admin extends User { }
}

namespace Phuby\Core {
    class ReflectionClassTest extends \ztest\UnitTestCase {

        function setup() {
            $this->user_reflection = new ReflectionClass(__CLASS__.NS.'User');
            $this->admin_reflection = new ReflectionClass(__CLASS__.NS.'Admin');
        }

        function test_getInstanceMethods() {
            assert_equal(array('instance_method'), array_keys($this->user_reflection->getInstanceMethods()));
            assert_equal(array(), $this->admin_reflection->getInstanceMethods());
        }

        function test_getParentClass() {
            assert_identical(ReflectionClass::instance(__NS__.'Object'), $this->user_reflection->getParentClass());

            $reflection = new ReflectionClass(__NS__.'Object');
            assert_null($reflection->getParentClass());
        }

        function test_getStaticMethods() {
            assert_equal(array('static_method'), array_keys($this->user_reflection->getStaticMethods()));
            assert_equal(array(), $this->admin_reflection->getStaticMethods());
        }

        function test_lookupMethod() {
            assert_equal('instance_method', $this->user_reflection->lookupMethod('instance_method', ReflectionClass::INSTANCE_METHOD)->name);
            assert_equal(false, $this->user_reflection->lookupMethod('invalid', ReflectionClass::INSTANCE_METHOD));

            assert_equal('static_method', $this->user_reflection->lookupMethod('static_method', ReflectionClass::STATIC_METHOD)->name);
            assert_equal(false, $this->user_reflection->lookupMethod('invalid', ReflectionClass::STATIC_METHOD));

            assert_equal(false, $this->admin_reflection->lookupMethod('instance_method', ReflectionClass::INSTANCE_METHOD));
            assert_equal(false, $this->admin_reflection->lookupMethod('static_method', ReflectionClass::STATIC_METHOD));
        }

        function test_protected_getCachedMethods() {
            $method = new \ReflectionMethod(__CORE__.'ReflectionClass', 'getCachedMethods');
            $method->setAccessible(true);
            $reflection = $this->user_reflection;

            $methods = $method->invoke($reflection);
            assert_array($methods);
            ensure(array_key_exists(ReflectionClass::INSTANCE_METHOD, $methods));
            ensure(array_key_exists(ReflectionClass::STATIC_METHOD, $methods));
            ensure(is_a(reset(reset($methods)), 'ReflectionMethod'));

            assert_equal($methods[ReflectionClass::INSTANCE_METHOD], $method->invoke($reflection, ReflectionClass::INSTANCE_METHOD));
            assert_equal($methods[ReflectionClass::STATIC_METHOD], $method->invoke($reflection, ReflectionClass::STATIC_METHOD));

            assert_throws('InvalidArgumentException', function() use ($method, $reflection) { $method->invoke($reflection, 'invalid'); });
        }

        function test_static_instance() {
            assert_identical(ReflectionClass::instance(__NS__.'Object'), ReflectionClass::instance(__NS__.'Object'));
        }

    }
}