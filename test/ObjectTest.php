<?php

namespace Phuby;

class ObjectTest extends \PHPUnit_Framework_TestCase {
    function setUp() {
        $this->Object = Phuby(__NAMESPACE__.'\Object');
    }

    function test_it_should_inherit_basic_object() {
        $this->assertContains(__NAMESPACE__.'\BasicObject', $this->Object->heritage);
    }

    function test_it_should_include_kernel() {
        $this->assertContains(__NAMESPACE__.'\Kernel', $this->Object->ancestors);
    }
}