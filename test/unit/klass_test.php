<?php

namespace KlassTest {
    class Base extends \Object { }
    class User extends Base { }
}

namespace {
    class KlassTest extends ztest\UnitTestCase {

        function setup() {
            $this->user_class = new Klass('KlassTest\User');
            $this->object_class = new Klass('Object');
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
        }

    }
}