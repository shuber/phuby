<?php

class BasicObjectTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException Phuby\NoMethodError
     */
    function testSendWithInvalidMethodName() {
        $object = new Phuby\Object;
        $object->__send__('invalid');
    }

    function testDefineMethod() {
        $object = new Phuby\Object;
        $object->singleton_class->define_method('testing', function() { return 'test'; });
        $this->assertEquals('test', $object->testing);
    }
}