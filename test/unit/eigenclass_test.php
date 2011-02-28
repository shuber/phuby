<?php

namespace EigenclassTest {
    class User extends \Object { }
    class Module { }
}

namespace {
    class EigenclassTest extends ztest\UnitTestCase {

        function setup() {
            $this->user_class = Klass::instance('EigenclassTest\User');
            $this->module = Klass::instance('EigenclassTest\Module');
            $this->user = new EigenclassTest\User;
        }

        function test_should_include_into_instance() {
            assert_not_in_array($this->module, $this->user_class->ancestors());
            assert_not_in_array($this->module, $this->user->__class()->ancestors());
            $this->user->__class()->__include($this->module, true);
            assert_in_array($this->module, $this->user->__class()->ancestors());
            assert_not_in_array($this->module, $this->user_class->ancestors());
        }

        function test_should_return_class_reference() {
            assert_identical($this->user_class, $this->user->__class()->reference());
        }

    }
}