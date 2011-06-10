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
            $this->user_class_ancestor_names = array(__CLASS__.NS.'User', __NS__.'Object');
            $this->object_class = $this->user_class->superclass();
        }

        function test___construct() {
            assert_throws('\InvalidArgumentException', function() { new Klass('Invalid'); });
        }

        function test_ancestors() {
            $ancestor_names = array_map(function($ancestor) { return $ancestor->name(); }, $this->user_class->ancestors());
            assert_equal($this->user_class_ancestor_names, $ancestor_names);
        }

        function test_clear_ancestors_cache() {
            $this->user_class->ancestors();
            assert_array($this->object_class->_ancestors);
            assert_array($this->user_class->_ancestors);

            $this->object_class->_dependants = array($this->user_class);
            $this->object_class->clear_ancestors_cache();

            assert_null($this->object_class->_ancestors);
            assert_null($this->user_class->_ancestors);
        }

        function test_name() {
            assert_equal($this->user_class_name, $this->user_class->name());
        }

        function test_reflection() {
            assert_equal(Core\ReflectionClass::instance($this->user_class_name), $this->user_class->reflection());
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