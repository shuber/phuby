<?php

namespace Phuby\ModuleTest {
    class Versionable extends \Phuby\Object {
        static $table_name = 'versions';
    }
}

namespace Phuby {
    class ModuleTest extends \ztest\UnitTestCase {

        function setup() {
            $this->module_name = __CLASS__.NS.'Versionable';
            $this->module = new Module($this->module_name);
        }

        function test_ancestors() {
            ensure(is_array($this->module->ancestors()));
        }

        function test_name() {
            assert_equal($this->module_name, $this->module->name());
        }

        function test_bind_instance_variables_to_properties() {
            $class = $this->module_name;
            $value = $class::$table_name;
            assert_equal($value, $this->module->table_name);

            $class::$table_name = 'testing';
            assert_equal('testing', $this->module->table_name);

            $this->module->table_name = $value;
            assert_all_equal($value, $class::$table_name, $this->module->table_name);
        }

        function test_instance() {
            $module = Module::instance($this->module_name);
            ensure(is_a($module, __NS__.'Module'));
            ensure($module === Module::instance($this->module_name));
            assert_equal($module->__id__(), Module::instance($this->module_name)->__id__());
        }

    }
}