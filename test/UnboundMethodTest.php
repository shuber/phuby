<?php

namespace Phuby;

class UnboundMethodTestUser {
    public $first;

    function __construct($first) {
        $this->first = $first;
    }

    function name($last = null) {
        $name = $this->first;

        if ($last)
            $name .= " $last";

        return $name;
    }
}

class UnboundMethodTest extends \PHPUnit_Framework_TestCase {
    function setUp() {
        $this->UnboundMethod = Phuby('Phuby\UnboundMethod');
        $this->owner = 'Phuby\Object';
        $this->name = 'test';
        $this->block = function() { return $this->name('jones'); };
        $this->subject = $this->UnboundMethod->new($this->owner, $this->name, $this->block);
    }

    function test_bind() {
        $receiver = new UnboundMethodTestUser('bob');
        $method = new Method($this->subject, $receiver);

        $this->assertEquals($method, $this->subject->bind($receiver));
    }

    function test_inspect() {
        $this->assertEquals('#<Phuby\UnboundMethod: Phuby\Object#test>', $this->subject->inspect);
    }

    function test_name() {
        $this->assertEquals($this->name, $this->subject->name);
    }

    function test_owner() {
        $this->assertEquals($this->owner, $this->subject->owner->name);
    }

    function to_proc() {
        $this->assertEquals($this->block, $this->subject->to_proc);
    }
}