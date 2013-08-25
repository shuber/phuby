<?php

namespace Phuby;

class MarshalTest extends \PHPUnit_Framework_TestCase {
    function setUp() {
        $this->Marshal = Phuby(__NAMESPACE__.'\Marshal');
        $this->unserialized = [1,2,3];
        $this->serialized = serialize($this->unserialized);
    }

    function test_dump() {
        $this->assertEquals($this->serialized, $this->Marshal->dump($this->unserialized));
    }

    function test_load() {
        $this->assertEquals($this->unserialized, $this->Marshal->load($this->serialized));
    }
}