<?php

namespace ObjectTest {
    class User extends \Object {

        function name() {
            return 'Tom';
        }

    }
}

namespace {
    class ObjectTest extends ztest\UnitTestCase {

        function setup() {
            $this->user = new ObjectTest\User;
        }

        function test_should_allocate() {
            ensure(is_a($this->user, 'ObjectTest\User'));
        }

        function test_should_pass_magic_get_calls_to_methods() {
            assert_equal('Tom', $this->user->name);
        }

    }
}