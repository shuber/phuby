<?php

namespace Phuby;

class BasicObjectTest extends \PHPUnit_Framework_TestCase {
    function setUp() {
        $this->BasicObject = Phuby(__NAMESPACE__.'\BasicObject');
    }

    function test_it_should_use_core() {
        $this->assertContains(__NAMESPACE__.'\Core', $this->BasicObject->traits);
    }
}