<?php

namespace Phuby;

class MethodTestMock {
    function test($message) {
        return __CLASS__." - $message";
    }
}

class MethodTest extends \PHPUnit_Framework_TestCase {
    function setUp() {
        $this->Method = Phuby('Phuby\Method');
        $this->receiver = new MethodTestMock;
        $this->unbound = Phuby('Phuby\UnboundMethod')->new('Phuby\MethodTestMock', 'test', function($arg) { return $this->test($arg); });
        $this->subject = $this->Method->new($this->unbound, $this->receiver);
    }

    function test_call() {
        $this->assertEquals(__NAMESPACE__.'\MethodTestMock - test', $this->subject->call('test'));
    }

    function test_inspect() {
        $this->assertEquals('#<Phuby\Method: Phuby\MethodTestMock#test>', $this->subject->inspect);
    }

    function test_receiver() {
        $this->assertEquals($this->receiver, $this->subject->receiver);
    }

    function test_splat() {
        $this->assertEquals(__NAMESPACE__.'\MethodTestMock - test', $this->subject->splat(['test']));
    }

    function test_to_proc() {
        $proc = $this->subject->to_proc;
        $this->assertTrue(is_a($proc, 'Closure'));
        $this->assertEquals(__NAMESPACE__.'\MethodTestMock - test', $proc('test'));
    }

    function test_unbind() {
        $this->assertEquals($this->unbound, $this->subject->unbind);
    }
}