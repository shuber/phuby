<?php

namespace Phuby\KlassTest {
    class User extends \Phuby\Object {
        static $table_name = 'users';
    }
}

namespace Phuby {
    class KlassTest extends \ztest\UnitTestCase {

        function setup() {
            $this->user_class_name = __CLASS__.NS.'User';
            $this->user_class = new Klass($this->user_class_name);
        }

        function test___construct() {
            assert_throws('\InvalidArgumentException', function() { new Klass('Invalid'); });
        }

        function test_ancestors() {
            ensure(is_array($this->user_class->ancestors()));
        }

        function test_name() {
            assert_equal($this->user_class_name, $this->user_class->name());
        }

        function test_superclass() {
            $object = $this->user_class->superclass();
            assert_equal(Klass::instance(__NS__.'Object'), $object);
            ensure(is_null($object->superclass()));
        }

        function test_protected_bind_instance_variables_to_properties() {
            $class = $this->user_class_name;
            $value = $class::$table_name;
            assert_equal($value, $this->user_class->table_name);

            $class::$table_name = 'testing';
            assert_equal('testing', $this->user_class->table_name);

            $this->user_class->table_name = $value;
            assert_all_equal($value, $class::$table_name, $this->user_class->table_name);
        }

        function test_static_instance() {
            $class = Klass::instance($this->user_class_name);
            ensure(is_a($class, __NS__.'Klass'));
            ensure($class === Klass::instance($this->user_class_name));
            assert_equal($class->__id__(), Klass::instance($this->user_class_name)->__id__());
        }

    }
}