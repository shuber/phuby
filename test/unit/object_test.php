<?php

namespace ObjectTest {
    class User extends \Object { }
}

namespace {
    class ObjectTest extends ztest\UnitTestCase {

        function setup() {
            $this->user = new ObjectTest\User;
        }

        function test_should_allocate() {
            ensure(is_a($this->user, 'ObjectTest\User'));
        }

    }
}