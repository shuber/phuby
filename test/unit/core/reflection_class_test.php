<?php

namespace Phuby\Core\ReflectionClassTest {
    class User extends \Phuby\Object { }
}

namespace Phuby\Core {
    class ReflectionClassTest extends \ztest\UnitTestCase {

        function setup() {
            $this->reflection = new ReflectionClass(__CLASS__.NS.'User');
        }

        function test_getParentClass() {
            assert_identical(ReflectionClass::instance(__NS__.'Object'), $this->reflection->getParentClass());

            $reflection = new ReflectionClass(__NS__.'Object');
            assert_null($reflection->getParentClass());
        }

        function test_static_instance() {
            assert_identical(ReflectionClass::instance(__NS__.'Object'), ReflectionClass::instance(__NS__.'Object'));
        }

    }
}