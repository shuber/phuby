<?php

namespace Phuby\Core\MethodTableTest {
    class User extends \Phuby\Object {
        function __id__() { return parent::__id__(); }
        function test() { }
    }
}

namespace Phuby\Core {
    class MethodTableTest extends \ztest\UnitTestCase {

        function setup() {
            $this->user_class_name = __CLASS__.NS.'User';
            $this->user_class = \Phuby\Klass::instance($this->user_class_name);
            $this->user_methods = $this->user_class->reflection()->getInstanceMethods();
            $this->object_class = $this->user_class->superclass();
            $this->object_methods = $this->object_class->reflection()->getInstanceMethods();
            $this->method_table = new MethodTable($this->user_class);
        }

        function test_clear_methods_cache() {
            $reflection = new \ReflectionProperty(__CORE__.'MethodTable', 'methods');
            $reflection->setAccessible(true);

            $this->method_table->methods();
            assert_array($reflection->getValue($this->method_table));
            $this->method_table->clear_methods_cache();
            assert_null($reflection->getValue($this->method_table));
        }

        function test_lookup() {
            assert_equal(false, $this->method_table->lookup('invalid'));
            assert_equal($this->user_methods['test'], $this->method_table->lookup('test'));
            assert_equal($this->user_methods['__id__'], $this->method_table->lookup('__id__'));
            assert_equal($this->object_methods['__id__'], $this->method_table->lookup('__id__', $this->user_class_name));
            assert_equal($this->object_methods['__send__'], $this->method_table->lookup('__send__'));
            assert_equal(false, $this->method_table->lookup('__send__', $this->object_class->name()));
        }

        function test_methods() {
            $methods = $this->method_table->methods();
            ensure(!isset($methods['invalid']));
            assert_equal(array(null => $this->user_methods['test']), $methods['test']);
            assert_equal(array(null => $this->object_methods['__send__']), $methods['__send__']);
            assert_equal(array(null => $this->user_methods['__id__'], $this->user_class_name => $this->object_methods['__id__']), $methods['__id__']);
        }

    }
}